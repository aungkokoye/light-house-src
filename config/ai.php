<?php

return [
    'enabled'  => env('AI_CHAT_ENABLED', false),
    'api_key'  => env('AI_API_KEY', ''),
    'model'    => env('AI_MODEL', 'gpt-4o-mini'),
    'base_uri' => env('AI_BASE_URI', 'https://api.openai.com/v1/'),
];
