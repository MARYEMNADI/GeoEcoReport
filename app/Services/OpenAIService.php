<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class OpenAIService
{
    /**
     * Send a request to OpenAI Responses API.
     *
     * @param array<int, array<string, string>> $messages
     */
    public function chat(array $messages): string
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY is not configured.');
        }

        $model = config('services.openai.model', 'gpt-4o-mini');
        $timeout = (int) config('services.openai.timeout', 20);

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout($timeout)
                ->retry(2, 500)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.2,
                ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    'OpenAI API error: ' . $response->body()
                );
            }

            $content = $response->json('choices.0.message.content');

            if (!is_string($content) || trim($content) === '') {
                throw new RuntimeException(
                    'OpenAI returned an empty response.'
                );
            }

            return trim($content);
        } catch (Throwable $e) {
            throw new RuntimeException(
                'OpenAI request failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}