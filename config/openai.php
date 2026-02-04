<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key
    |--------------------------------------------------------------------------
    |
    | Your OpenAI API key. You can get this from your OpenAI dashboard at
    | https://platform.openai.com/api-keys
    |
    */
    'api_key' => env('OPENAI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Organization ID
    |--------------------------------------------------------------------------
    |
    | Your OpenAI organization ID (optional). This is only needed if you
    | belong to multiple organizations.
    |
    */
    'organization_id' => env('OPENAI_ORGANIZATION_ID'),

    /*
    |--------------------------------------------------------------------------
    | Default Model
    |--------------------------------------------------------------------------
    |
    | The default OpenAI model to use for tax calculations.
    | gpt-3.5-turbo is recommended for cost-effectiveness.
    |
    */
    'default_model' => env('OPENAI_DEFAULT_MODEL', 'gpt-3.5-turbo'),

    /*
    |--------------------------------------------------------------------------
    | Enable OpenAI Integration
    |--------------------------------------------------------------------------
    |
    | Set to false to disable OpenAI integration and use only local calculations.
    | This is useful if you don't have an API key or want to save costs.
    |
    */
    'enabled' => env('OPENAI_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Fallback to Local Calculation
    |--------------------------------------------------------------------------
    |
    | If OpenAI fails, should the system fall back to local tax calculation?
    | Recommended to keep this as true for reliability.
    |
    */
    'fallback_to_local' => env('OPENAI_FALLBACK_TO_LOCAL', true),
];
