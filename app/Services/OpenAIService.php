<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;

    private string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
    }

    public function extractRecipeFromContent(string $content, string $url): array
    {
        if (empty($this->apiKey) || $this->apiKey === 'sk-dummy-key-replace-with-real-key') {
            return [
                'success' => false,
                'error' => 'OpenAI API key not configured. Please add your API key to the .env file.',
            ];
        }

        try {
            $prompt = $this->buildRecipeExtractionPrompt($content, $url);

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl.'/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful assistant that extracts recipe information from webpage content. Always respond with valid JSON only.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'max_tokens' => 2000,
                    'temperature' => 0.1,
                ]);

            if (! $response->successful()) {
                Log::error('OpenAI API request failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to process recipe with OpenAI. Status: '.$response->status(),
                ];
            }

            $data = $response->json();

            if (! isset($data['choices'][0]['message']['content'])) {
                return [
                    'success' => false,
                    'error' => 'Invalid response from OpenAI API.',
                ];
            }

            $recipeJson = $data['choices'][0]['message']['content'];
            $recipeData = json_decode($recipeJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse OpenAI response as JSON', [
                    'response' => $recipeJson,
                    'error' => json_last_error_msg(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to parse recipe data from OpenAI response.',
                ];
            }

            return [
                'success' => true,
                'recipe' => $this->normalizeRecipeData($recipeData),
            ];

        } catch (RequestException $e) {
            Log::error('OpenAI API request exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to connect to OpenAI API: '.$e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error('Unexpected error in OpenAI service', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An unexpected error occurred while processing the recipe.',
            ];
        }
    }

    private function buildRecipeExtractionPrompt(string $content, string $url): string
    {
        return "Extract recipe information from the following webpage content and return it as JSON with this exact structure:

{
  \"name\": \"Recipe Name\",
  \"description\": \"Brief description (optional)\",
  \"prep_time\": 15,
  \"cook_time\": 30,
  \"servings\": 4,
  \"ingredients\": [
    {\"name\": \"ingredient name\", \"quantity\": \"1 cup\"},
    {\"name\": \"another ingredient\", \"quantity\": \"2 tbsp\"}
  ],
  \"instructions\": [
    \"Step 1 instruction\",
    \"Step 2 instruction\"
  ]
}

Rules:
- Times should be in minutes as integers (null if not found)
- Servings should be an integer (null if not found)
- Include all ingredients with their quantities
- Include all cooking steps in order
- If no recipe is found, return: {\"error\": \"No recipe found on this page\"}
- Return only valid JSON, no additional text

Webpage URL: {$url}

Webpage Content:
{$content}";
    }

    private function normalizeRecipeData(array $data): array
    {
        // Handle error case
        if (isset($data['error'])) {
            return ['error' => $data['error']];
        }

        // Normalize the recipe data structure
        return [
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? null,
            'prep_time' => is_numeric($data['prep_time'] ?? null) ? (int) $data['prep_time'] : null,
            'cook_time' => is_numeric($data['cook_time'] ?? null) ? (int) $data['cook_time'] : null,
            'servings' => is_numeric($data['servings'] ?? null) ? (int) $data['servings'] : null,
            'ingredients' => $this->normalizeIngredients($data['ingredients'] ?? []),
            'instructions' => $this->normalizeInstructions($data['instructions'] ?? []),
        ];
    }

    private function normalizeIngredients(array $ingredients): array
    {
        $normalized = [];

        foreach ($ingredients as $ingredient) {
            if (is_array($ingredient) && isset($ingredient['name'])) {
                $normalized[] = [
                    'name' => trim($ingredient['name']),
                    'quantity' => trim($ingredient['quantity'] ?? ''),
                ];
            } elseif (is_string($ingredient)) {
                // Handle case where ingredient is just a string
                $normalized[] = [
                    'name' => trim($ingredient),
                    'quantity' => '',
                ];
            }
        }

        return $normalized;
    }

    private function normalizeInstructions(array $instructions): array
    {
        $normalized = [];

        foreach ($instructions as $instruction) {
            if (is_string($instruction) && ! empty(trim($instruction))) {
                $normalized[] = trim($instruction);
            }
        }

        return $normalized;
    }
}
