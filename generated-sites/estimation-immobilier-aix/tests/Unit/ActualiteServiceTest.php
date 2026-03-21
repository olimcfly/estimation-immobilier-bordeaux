<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ActualiteService;

/**
 * Tests for the ActualiteService automated news pipeline.
 *
 * Verifies:
 *  - searchNews() returns structured results with fallback
 *  - generateArticleFromIdeas() produces valid article structure
 *  - slugify() in cron script works correctly
 *  - CLI argument parsing (--dry-run, --query)
 */
final class ActualiteServiceTest extends TestCase
{
    private ActualiteService $service;

    protected function setUp(): void
    {
        $this->service = new ActualiteService();
    }

    public function testSearchNewsReturnsFallbackWithoutApiKey(): void
    {
        $result = $this->service->searchNews();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('query', $result);
        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('source', $result);
        $this->assertSame('fallback', $result['source']);
        $this->assertCount(5, $result['results']);
    }

    public function testSearchNewsWithCustomQuery(): void
    {
        $result = $this->service->searchNews('prix immobilier Aix-en-Provence 2026');

        $this->assertSame('prix immobilier Aix-en-Provence 2026', $result['query']);
        $this->assertNotEmpty($result['results']);
    }

    public function testFallbackResultsHaveRequiredKeys(): void
    {
        $result = $this->service->searchNews();

        foreach ($result['results'] as $idea) {
            $this->assertArrayHasKey('title', $idea);
            $this->assertArrayHasKey('summary', $idea);
            $this->assertArrayHasKey('angle', $idea);
        }
    }

    public function testGenerateArticleFallbackWithoutApiKey(): void
    {
        $ideas = [
            ['title' => 'Test article', 'summary' => 'Un résumé test', 'angle' => 'Angle test'],
        ];

        $article = $this->service->generateArticleFromIdeas($ideas, 'test query');

        $this->assertArrayHasKey('title', $article);
        $this->assertArrayHasKey('meta_title', $article);
        $this->assertArrayHasKey('meta_description', $article);
        $this->assertArrayHasKey('excerpt', $article);
        $this->assertArrayHasKey('content', $article);
        $this->assertArrayHasKey('image_prompt', $article);
        $this->assertNotEmpty($article['title']);
        $this->assertNotEmpty($article['content']);
    }

    public function testGenerateArticleFallbackContainsCta(): void
    {
        $ideas = [
            ['title' => 'Marché immobilier Aix-en-Provence', 'summary' => 'Les prix évoluent', 'angle' => 'Analyse'],
        ];

        $article = $this->service->generateArticleFromIdeas($ideas, 'test');

        $this->assertStringContainsString('/estimation', $article['content']);
    }

    public function testGenerateArticleFallbackWithEmptyIdeas(): void
    {
        $article = $this->service->generateArticleFromIdeas([], 'test');

        $this->assertArrayHasKey('title', $article);
        $this->assertNotEmpty($article['content']);
    }

    public function testCronScriptSlugify(): void
    {
        // Test the slugify function from cron script
        require_once __DIR__ . '/../../cron/generate-actualite.php.test-helpers.php';

        $this->assertSame('hello-world', cronSlugify('Hello World'));
        $this->assertSame('estimation-immobilière-aix', cronSlugify('Estimation Immobilière Aix-en-Provence'));
        $this->assertSame('actualite', cronSlugify(''));
        $this->assertSame('test-123', cronSlugify('  Test 123  '));
    }

    public function testCronScriptExists(): void
    {
        $cronScript = __DIR__ . '/../../cron/generate-actualite.php';
        $this->assertFileExists($cronScript);

        $content = file_get_contents($cronScript);
        $this->assertStringContainsString('--dry-run', $content);
        $this->assertStringContainsString('--query=', $content);
        $this->assertStringContainsString('runAutomatedPipeline', $content);
    }

    public function testCronScriptHasShebang(): void
    {
        $cronScript = __DIR__ . '/../../cron/generate-actualite.php';
        $firstLine = fgets(fopen($cronScript, 'r'));

        $this->assertStringStartsWith('#!/usr/bin/env php', $firstLine);
    }
}
