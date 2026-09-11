<?php

namespace Notion\Test\Integration;

use Notion\Authentication\ExternalAccount;
use Notion\Exceptions\ApiException;
use Notion\Notion;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_create_token_invalid_code(): void
    {
        $notion = Notion::create("dummy_client_id:dummy_client_secret");

        $this->expectException(ApiException::class);
        $notion->authentication()->createToken("invalid_authorization_code");
    }

    public function test_create_token_with_external_account(): void
    {
        $notion = Notion::create("dummy_client_id:dummy_client_secret");
        $externalAccount = ExternalAccount::create("acc_123", "Acme Corp");

        $this->expectException(ApiException::class);
        $notion->authentication()->createToken(
            code: "invalid_authorization_code",
            redirectUri: "https://example.com/callback",
            externalAccount: $externalAccount,
        );
    }

    public function test_refresh_token_invalid(): void
    {
        $notion = Notion::create("dummy_client_id:dummy_client_secret");

        $this->expectException(ApiException::class);
        $notion->authentication()->refreshToken("invalid_refresh_token");
    }

    public function test_introspect_token_invalid(): void
    {
        $notion = Notion::create("dummy_client_id:dummy_client_secret");

        $this->expectException(ApiException::class);
        $notion->authentication()->introspectToken("invalid_token");
    }

    public function test_revoke_token_invalid(): void
    {
        $notion = Notion::create("dummy_client_id:dummy_client_secret");

        $this->expectException(ApiException::class);
        $notion->authentication()->revokeToken("invalid_token");
    }
}
