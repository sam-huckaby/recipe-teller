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

    public function extractRecipeFromContent(string $content, string $url, array $images = []): array
    {
        if (empty($this->apiKey) || $this->apiKey === 'sk-dummy-key-replace-with-real-key') {
            return [
                'success' => false,
                'error' => 'OpenAI API key not configured. Please add your API key to the .env file.',
            ];
        }

        try {
            $prompt = $this->buildRecipeExtractionPrompt($content, $url, $images);

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

            // Strip markdown code block formatting if present, but handle plain JSON too
            $recipeJson = trim($recipeJson);
            if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $recipeJson, $matches)) {
                $recipeJson = trim($matches[1]);
            }

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

    private function buildRecipeExtractionPrompt(string $content, string $url, array $images = []): string
    {
        $imageInfo = '';
        if (! empty($images)) {
            $imageInfo = "\n\nAvailable images found on the page:\n";
            foreach (array_slice($images, 0, 3) as $index => $image) {
                $imageInfo .= ($index + 1).'. '.$image['url']." (alt: '".($image['alt'] ?: 'none')."')\n";
            }
            $imageInfo .= "\nChoose the most appropriate image URL for the recipe from the list above, or set to null if none are suitable.";
        }

        return "Extract recipe information from the following webpage content and return it as JSON with this exact structure:

{
  \"name\": \"Recipe Name\",
  \"description\": \"Brief description (optional)\",
  \"prep_time\": 15,
  \"cook_time\": 30,
  \"servings\": 4,
  \"image_url\": \"https://example.com/recipe-image.jpg\",
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
- image_url should be the main recipe image URL from the page (null if not found)
- Include all ingredients with their quantities
- Include all cooking steps in order
- If no recipe is found, return: {\"error\": \"No recipe found on this page\"}
- Return only valid JSON, no additional text

Webpage URL: {$url}
{$imageInfo}

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
            'image_url' => $this->normalizeImageUrl($data['image_url'] ?? null),
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

    private function normalizeImageUrl(?string $imageUrl): ?string
    {
        if (empty($imageUrl)) {
            return null;
        }

        $imageUrl = trim($imageUrl);

        // Validate URL format
        if (! filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        // Basic check for image file extensions (optional)
        $validExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $extension = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

        // If no extension or valid extension, return the URL
        if (empty($extension) || in_array($extension, $validExtensions)) {
            return $imageUrl;
        }

        return null;
    }
}
