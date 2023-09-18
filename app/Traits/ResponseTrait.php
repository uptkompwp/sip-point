<?php

namespace App\Traits;

use App\Enums\ContextErrorEnum;
use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    function responseSuccess(mixed $data = [], int $status = 200, $headers = []): JsonResponse
    {
        return response()->json($data, $status)->withHeaders($headers);
    }
    function responseError(ContextErrorEnum $context, mixed $error = [], int $status = 400, $headers = []): JsonResponse
    {
        return response()->json([...$error, 'context' => $context], $status)->withHeaders($headers);
    }
}
