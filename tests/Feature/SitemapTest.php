<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitemapTest extends TestCase
{
    /**
     * Test the sitemap.xml returns a successful XML response.
     */
    public function test_sitemap_returns_successful_xml_response(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        
        $content = $response->getContent();
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString('<loc>' . url('/') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . route('privacy') . '</loc>', $content);
        $this->assertStringContainsString('<loc>' . route('terms') . '</loc>', $content);
    }
}
