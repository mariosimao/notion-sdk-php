<?php

namespace Notion\Sessions;

use Generator;
use Notion\Configuration;
use Notion\Exceptions\ApiException;
use Notion\Infrastructure\Http;
use Notion\Infrastructure\ServerSentEvents;
use Notion\Sessions\Events\EventQuery;
use Notion\Sessions\Events\EventQueryResult;
use Notion\Sessions\Events\SessionEvent;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

/**
 * Chat with Custom Agents through the Notion Agent API.
 *
 * @psalm-import-type SessionJson from Session
 * @psalm-import-type QueryResultJson from QueryResult
 * @psalm-import-type EventQueryResultJson from EventQueryResult
 * @psalm-import-type StreamEventJson from StreamEvent
 */
final readonly class Client
{
    /** Oldest Notion API version supporting the Agent API. */
    public const MIN_API_VERSION = "2026-03-11";

    /**
     * @internal Use `\Notion\Notion::sessions()` instead
     */
    public function __construct(
        private Configuration $config,
    ) {
    }

    /**
     * Start a session or send a message to an existing one.
     *
     * The agent works asynchronously: the returned session is usually `queued`
     * or `in_progress`. Use `find()` to poll it, `stream()` to follow the turn
     * live, and `queryEvents()` to read the reply.
     */
    public function send(Message $message): Session
    {
        return $this->update($message->toArray());
    }

    /** Answer the actions required by a session in `requires_action` status. */
    public function submitActions(ActionSubmission $submission): Session
    {
        return $this->update($submission->toArray());
    }

    /**
     * Send a message or submit actions and stream the turn as server-sent events.
     *
     * The request is sent immediately. Events are yielded as they are read
     * from the response body, so whether they arrive in real time depends on
     * the PSR-18 client streaming the response body.
     *
     * @return Generator<int, StreamEvent, mixed, void>
     */
    public function stream(Message|ActionSubmission $request): Generator
    {
        return $this->updateStream($request->toArray());
    }

    /**
     * Reconnect to a session stream, replaying events after a committed event.
     *
     * @param string $continueFrom ID of the last committed event received
     *
     * @return Generator<int, StreamEvent, mixed, void>
     */
    public function resumeStream(string $sessionId, string $continueFrom): Generator
    {
        return $this->updateStream([
            "session_id" => $sessionId,
            "continue_from" => $continueFrom,
        ]);
    }

    public function find(string $sessionId): Session
    {
        $url = "https://api.notion.com/v1/sessions/{$sessionId}";
        $request = $this->createRequest($url);

        /** @psalm-var SessionJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Session::fromArray($body);
    }

    /** Find sessions the integration can read. */
    public function query(Query|null $query = null): QueryResult
    {
        $request = $this->createJsonRequest(
            "https://api.notion.com/v1/sessions/query",
            ($query ?? Query::create())->toArray(),
        );

        /** @psalm-var QueryResultJson $body */
        $body = Http::sendRequest($request, $this->config);

        return QueryResult::fromArray($body);
    }

    /**
     * Find all sessions matching a query, following pagination.
     *
     * @return list<Session>
     */
    public function queryAll(Query|null $query = null): array
    {
        $query = ($query ?? Query::create())->changePageSize(Query::MAX_PAGE_SIZE);

        $sessions = [];
        do {
            $result = $this->query($query);
            $sessions = [...$sessions, ...$result->sessions];
            $query = $query->changeStartCursor($result->nextCursor);
        } while ($result->hasMore && $result->nextCursor !== null);

        return $sessions;
    }

    /** Retrieve a page of events from a session. */
    public function queryEvents(string $sessionId, EventQuery|null $query = null): EventQueryResult
    {
        $request = $this->createJsonRequest(
            "https://api.notion.com/v1/sessions/{$sessionId}/events/query",
            ($query ?? EventQuery::create())->toArray(),
        );

        /** @psalm-var EventQueryResultJson $body */
        $body = Http::sendRequest($request, $this->config);

        return EventQueryResult::fromArray($body);
    }

    /**
     * Retrieve all events from a session, following pagination.
     *
     * @return list<SessionEvent>
     */
    public function queryAllEvents(string $sessionId, EventQuery|null $query = null): array
    {
        $query = ($query ?? EventQuery::create())->changePageSize(EventQuery::MAX_PAGE_SIZE);

        $events = [];
        do {
            $result = $this->queryEvents($sessionId, $query);
            $events = [...$events, ...$result->events];
            $query = $query->changeStartCursor($result->nextCursor);
        } while ($result->hasMore && $result->nextCursor !== null);

        return $events;
    }

    /**
     * Cancel an in-progress session.
     *
     * @param string|null $eventId Event identifying the turn to cancel. Defaults to the current turn.
     */
    public function cancel(string $sessionId, string|null $eventId = null): Session
    {
        $data = $eventId !== null ? [ "event_id" => $eventId ] : [];
        $request = $this->createJsonRequest(
            "https://api.notion.com/v1/sessions/{$sessionId}/cancel",
            $data,
        );

        /** @psalm-var SessionJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Session::fromArray($body);
    }

    private function update(array $data): Session
    {
        $request = $this->createJsonRequest("https://api.notion.com/v1/sessions", $data);

        /** @psalm-var SessionJson $body */
        $body = Http::sendRequest($request, $this->config);

        return Session::fromArray($body);
    }

    /** @return Generator<int, StreamEvent, mixed, void> */
    private function updateStream(array $data): Generator
    {
        $request = $this->createJsonRequest("https://api.notion.com/v1/sessions", $data)
            ->withHeader("Accept", "text/event-stream");

        $response = $this->config->httpClient->sendRequest($request);
        if ($response->getStatusCode() >= 400) {
            throw ApiException::fromResponse($response);
        }

        return self::parseStream($response);
    }

    /** @return Generator<int, StreamEvent, mixed, void> */
    private static function parseStream(ResponseInterface $response): Generator
    {
        foreach (ServerSentEvents::parse($response->getBody()) as $sse) {
            $data = json_decode($sse["data"], true);
            if (!is_array($data)) {
                continue;
            }

            if (!isset($data["type"]) && $sse["event"] !== null) {
                $data["type"] = $sse["event"];
            }

            if (!isset($data["type"]) || !is_string($data["type"])) {
                continue;
            }

            /** @psalm-var StreamEventJson $envelope */
            $envelope = $data;

            yield StreamEvent::fromArray($envelope);
        }
    }

    private function createRequest(string $url): RequestInterface
    {
        $version = strcmp($this->config->version, self::MIN_API_VERSION) < 0
            ? self::MIN_API_VERSION
            : $this->config->version;

        return Http::createRequest($url, $this->config)
            ->withHeader("Notion-Version", $version);
    }

    private function createJsonRequest(string $url, array $data): RequestInterface
    {
        $request = $this->createRequest($url)
            ->withMethod("POST")
            ->withHeader("Content-Type", "application/json");

        $request->getBody()->write(json_encode($data === [] ? new stdClass() : $data));

        return $request;
    }
}
