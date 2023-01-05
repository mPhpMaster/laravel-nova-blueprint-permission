<?php

namespace App\Helpers\Classes;

use App\Http\Resources\Abstracts\AbstractResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 */
class ResponseUtil
{
    public static function makeResponseCollection(string $message, \Illuminate\Http\Resources\Json\JsonResource|\Traversable|array $data): \Illuminate\Http\Resources\Json\JsonResource|\Traversable|array
    {
        $success = true;
        if( $data instanceof \Illuminate\Http\Resources\Json\JsonResource ) {
            $data->additional(compact('success', 'message'));
            return $data;
        }

        if( $data instanceof AbstractResource ) {
            return $data->success($success, $message);
        }

        $data[ 'success' ] = $success;
        $data[ 'message' ] = $message;

        return $data;
    }

    public static function makeResponse(string $message, mixed $data): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    public static function makeError(string $message, array $data = []): array
    {
        $res = [
            'success' => false,
            'message' => $message,
            'errors' => [],
        ];

        if( !empty($data) ) {
            $res[ 'data' ] = $data;
        }

        return $res;
    }
}
