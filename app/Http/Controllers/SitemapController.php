<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap xml.
     */
    public function index(): Response
    {
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now()->startOfMonth()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('privacy'),
                'lastmod' => '2026-06-20T00:00:00+00:00',
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('terms'),
                'lastmod' => '2026-06-20T00:00:00+00:00',
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
        ];

        $content = view('sitemap', compact('urls'))->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
