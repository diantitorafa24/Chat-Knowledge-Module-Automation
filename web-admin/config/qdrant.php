<?php

return [
    'url' => env('QDRANT_URL', 'http://127.0.0.1:6333'),
    'api_key' => env('QDRANT_API_KEY'),
    'collection' => env('QDRANT_COLLECTION', 'cika_chunks'),
];
