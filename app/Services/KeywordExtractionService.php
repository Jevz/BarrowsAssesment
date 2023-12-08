<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class KeywordExtractionService
{
    private string $postContent;
    private array $otherPostContent;

    function __construct(Post $post)
    {
        $this->postContent = $post->content;
        $this->otherPostContent = Post::query()->where('id', '!=', $post->id)
                                      ->pluck('content')
                                      ->toArray();
    }

    public function extractTopKeywords($numberOfKeywords = 5): array
    {
        $terms = $this->preprocessText($this->postContent);
        $keywords = [];

        foreach ($terms as $term) {
            $keywords[$term] = $this->calculateTFIDF($term);
        }

        // Sort keywords by their TF-IDF scores in descending order
        arsort($keywords);

        // Select the top keywords
        $topKeywords = array_slice($keywords, 0, $numberOfKeywords);
        return array_keys($topKeywords);
    }

    private function calculateTermFrequency($term): float|int
    {
        $words = preg_split('/\s+/', $this->postContent);
        $wordCount = count($words);
        $termCount = 0;

        $lowerCaseTerm = Str::lower($term);
        foreach ($words as $word) {
            if (Str::lower($word) === $lowerCaseTerm) {
                $termCount++;
            }
        }

        return $termCount / $wordCount;
    }

    private function calculateInverseDocumentFrequency($term): float|int
    {
        $documentCount = count($this->otherPostContent);
        $documentsWithTerm = 0;

        foreach ($this->otherPostContent as $document) {
            if (Str::contains($document, $term)) {
                $documentsWithTerm++;
            }
        }

        return $documentsWithTerm > 0 ? log($documentCount / $documentsWithTerm) : 0.00;
    }

    private function calculateTFIDF($term): float|int
    {
        $tf = $this->calculateTermFrequency($term);
        $idf = $this->calculateInverseDocumentFrequency($term);

        return $tf * $idf;
    }

    private function preProcessText($text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text); // Remove punctuation
        $words = preg_split('/\s+/', $text);

        $stopWords = config('stopwords');
        return array_diff($words, $stopWords);
    }
}
