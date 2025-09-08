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

            return [
                'success' => true,
                'content' => $textContent,
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
}
