<?php

namespace App\Enum;

/*
|--------------------------------------------------------------------------
| API Response Codes
|--------------------------------------------------------------------------
|
| Here is where you create constants for api response codes to ensure
| uniformity across all endpoints served by the application.
|
*/

final class ResponseCodes
{
    public const UNAUTHORIZED = 401;
    public const SOMETHING_WENT_WRONG = 500;
    public const HTTP_OK = 200;
}