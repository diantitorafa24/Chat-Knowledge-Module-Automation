<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RagSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RagController extends Controller
{
    public function search(Request $request, RagSearchService $rag)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'min:2', 'max:' . (int) config('rag.max_question_length')],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        try {
            return response()->json($rag->search($data['question'], $data['module_id'] ?? null, $data['limit'] ?? null));
        } catch (\Throwable $e) {
            Log::error('Pencarian RAG gagal', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Pencarian gagal.',
                'error' => $e->getMessage(),
            ], 503);
        }
    }
}
