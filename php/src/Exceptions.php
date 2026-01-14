<?php

declare(strict_types=1);

namespace TNKB;

/**
 * Base exception for all TNKB errors
 */
class TNKBException extends \Exception
{
}

/**
 * Thrown when plate number format is invalid
 */
class InvalidPlateException extends TNKBException
{
}

/**
 * Thrown when API returns an error
 */
class APIException extends TNKBException
{
}

/**
 * Thrown when network connection fails
 */
class NetworkException extends APIException
{
}

/**
 * Thrown when request times out
 */
class TimeoutException extends NetworkException
{
}

/**
 * Thrown when validation fails
 */
class ValidationException extends TNKBException
{
}
