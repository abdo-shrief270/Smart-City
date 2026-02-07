<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleFirebaseSync(FirebaseService $firebase): JsonResponse
    {
        try {
            $firebase->syncToDatabase();
            return response()->json(['message' => 'Sync triggered successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
