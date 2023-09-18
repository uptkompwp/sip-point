<?php

namespace App\Enums;

enum ContextErrorEnum: string
{
    case VALIDATIONS = "VALIDATIONS";
    case INVALID_CREDENTIALS = "INVALID_CREDENTIALS";
    case INTERNAL_SERVER_ERROR = "INTERNAL_SERVER_ERROR";
    case UNAUTHORIZED = "UNAUTHORIZED";
    case FAIL = "FAIL";
    case NOT_FOUND = "NOT_FOUND";
}
