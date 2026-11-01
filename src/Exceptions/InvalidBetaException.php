<?php

namespace Notion\Exceptions;

/**
 * Thrown when Notion rejects a request to a beta feature (`invalid_beta`).
 *
 * Usually means the feature is not available to the integration or its user.
 */
final class InvalidBetaException extends ApiException
{
}
