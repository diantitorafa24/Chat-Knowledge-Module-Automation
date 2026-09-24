<?php

return [
    'api_token' => env('API_TOKEN'),
    'min_score' => (float) env('RAG_MIN_SCORE', 0.60),
    'top_k' => (int) env('RAG_TOP_K', 5),
    'max_question_length' => 500,
];
