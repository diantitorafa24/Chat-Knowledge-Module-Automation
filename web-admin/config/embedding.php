<?php

return [
    'api_key' => env('GEMINI_API_KEY'),
    'model' => env('GEMINI_EMBEDDING_MODEL', 'gemini-embedding-2'),
    'dimensions' => (int) env('GEMINI_EMBEDDING_DIMENSIONS', 768),
    'endpoint' => 'https://generativelanguage.googleapis.com/v1beta',
];
