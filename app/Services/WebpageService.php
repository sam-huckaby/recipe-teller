<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebpageService
{
    public function fetchContent(string $url): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                ])
                ->get($url);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Failed to fetch webpage. HTTP status: '.$response->status(),
                ];
            }

            $content = $response->body();

            // Extract text content from HTML
            $textContent = $this->extractTextFromHtml($content);

            if (empty($textContent)) {
                return [
                    'success' => false,
                    'error' => 'No readable content found on the webpage.',
                ];
            }

            // Extract potential recipe images from HTML
            $imageUrls = $this->extractImageUrls($content, $url);

            return [
                'success' => true,
                'content' => $textContent,
                'images' => $imageUrls,
                'url' => $url,
            ];

        } catch (RequestException $e) {
            Log::error('Webpage fetch failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to fetch webpage: '.$e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error('Unexpected error fetching webpage', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An unexpected error occurred while fetching the webpage.',
            ];
        }
    }

    private function extractTextFromHtml(string $html): string
    {
        // Remove script and style elements
        $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
        $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $html);

        // Convert HTML to plain text
        $text = strip_tags($html);

        // Clean up whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Limit content length to avoid hitting API limits
        if (strlen($text) > 10000) {
            $text = substr($text, 0, 10000).'...';
        }

        return $text;
    }

    private function extractImageUrls(string $html, string $baseUrl): array
    {
        $imageUrls = [];

        // Use DOMDocument to parse HTML and find images
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            if (empty($src)) {
                continue;
            }

            // Convert relative URLs to absolute
            if (strpos($src, 'http') !== 0) {
                $src = $this->resolveUrl($src, $baseUrl);
            }

            // Skip data URLs and SVGs
            if (strpos($src, 'data:') === 0 || strpos($src, '.svg') !== false) {
                continue;
            }

            // Check for recipe-related images based on alt text or class names
            $alt = $img->getAttribute('alt');
            $class = $img->getAttribute('class');

            // Prioritize images that seem recipe-related
            $isRecipeImage = $this->isLikelyRecipeImage($alt, $class, $src);

            $imageUrls[] = [
                'url' => $src,
                'alt' => $alt,
                'class' => $class,
                'is_recipe_related' => $isRecipeImage,
            ];
        }

        // Sort by recipe relevance
        usort($imageUrls, function ($a, $b) {
            return $b['is_recipe_related'] <=> $a['is_recipe_related'];
        });

        return array_slice($imageUrls, 0, 5); // Return top 5 images
    }

    private function isLikelyRecipeImage(string $alt, string $class, string $src): bool
    {
        $recipeKeywords = ['recipe', 'dish', 'food', 'cooking', 'meal', 'ingredient'];

        $searchText = strtolower($alt.' '.$class.' '.$src);

        foreach ($recipeKeywords as $keyword) {
            if (strpos($searchText, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private function resolveUrl(string $relativeUrl, string $baseUrl): string
    {
        if (strpos($relativeUrl, '//') === 0) {
            return 'https:'.$relativeUrl;
        }

        if (strpos($relativeUrl, '/') === 0) {
            $parsedBase = parse_url($baseUrl);

            return $parsedBase['scheme'].'://'.$parsedBase['host'].$relativeUrl;
        }

        return rtrim($baseUrl, '/').'/'.ltrim($relativeUrl, '/');
    }
}
