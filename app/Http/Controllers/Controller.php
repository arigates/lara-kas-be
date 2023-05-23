<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function showedResponse($data): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ]);
    }

    public function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'data not found',
        ], 404);
    }

    public function createdResponse($data): JsonResponse
    {
        return response()->json([
            'message' => 'data created',
            'data' => $data,
        ], 201);
    }

    public function updatedResponse($data): JsonResponse
    {
        return response()->json([
            'message' => 'data updated',
            'data' => $data,
        ]);
    }

    public function deletedResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'data deleted',
        ]);
    }
}
