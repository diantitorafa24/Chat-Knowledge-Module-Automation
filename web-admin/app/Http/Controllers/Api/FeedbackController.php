<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'callback_data' => ['nullable', 'string'],
            'telegram_user_id' => ['nullable'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $rating = null;
        if (! empty($data['callback_data']) && preg_match('/^fb_(\d+)_?(\d*)$/', $data['callback_data'], $m)) {
            $rating = (int) $m[1];
            $chatLogId = $m[2] !== '' ? (int) $m[2] : null;
        }

        // Kalau chat_log_id kosong (bug n8n), ambil chat log TERAKHIR dari user ini.
        if (empty($chatLogId) && ! empty($data['telegram_user_id'])) {
            $chatLogId = ChatLog::where('telegram_user_id', $data['telegram_user_id'])
                ->latest('id')
                ->value('id');
        }

        if (! $chatLogId || ! ChatLog::where('id', $chatLogId)->exists()) {
            return response()->json(['message' => 'chat_log_id tidak valid.'], 422);
        }

        if (! in_array($rating, [0, 1], true)) {
            return response()->json(['message' => 'rating harus 0 atau 1.'], 422);
        }

        $feedback = Feedback::create([
            'chat_log_id' => $chatLogId,
            'telegram_user_id' => $data['telegram_user_id'] ?? null,
            'rating' => $rating,
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json(['message' => 'Terima kasih atas feedback-nya!', 'id' => $feedback->id]);
    }
}
