<?php

namespace App\Http\Controllers;

use App\Models\Events\Event;
use App\Models\News\News;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    private const PAGES = [
        '/' => 1.0,
        '/events' => 0.9,
        '/news' => 0.9,
        '/airports' => 0.9,
        '/vfr' => 0.9,
        '/join' => 0.8,
        '/staff' => 0.7,
        '/roster' => 0.7,
        '/preferredrouting' => 0.7,
        '/instructors' => 0.6,
        '/pdc' => 0.6,
        '/policies' => 0.6,
        '/meetingminutes' => 0.4,
        '/branding' => 0.3,
        '/privacy' => 0.3,
        '/about' => 0.3,
    ];

    public function robots(Request $request): Response
    {
        $lines = $this->isMainSite($request)
            ? [
                'User-agent: PetalBot',
                'Disallow: /',
                '',
                'User-agent: *',
                'Disallow: /admin',
                'Disallow: /dashboard',
                'Disallow: /connect',
                'Disallow: /roster/',
                'Disallow: /feedback',
                'Disallow: /renew',
                'Disallow: /editmap',
                'Disallow: /github',
                '',
                'Sitemap: '.url('/sitemap.xml'),
            ]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function sitemap(Request $request): Response
    {
        abort_unless($this->isMainSite($request), 404);

        $urls = collect(self::PAGES)->map(fn ($priority, $path) => [
            'loc' => url($path),
            'lastmod' => null,
            'priority' => $priority,
        ])->values();

        Event::query()->whereNotNull('slug')->orderByDesc('start_timestamp')->get(['slug', 'updated_at'])
            ->each(fn ($event) => $urls->push([
                'loc' => url('/events/'.$event->slug),
                'lastmod' => $event->updated_at,
                'priority' => 0.6,
            ]));

        News::query()->where('visible', true)->whereNotNull('slug')->orderByDesc('published')->get(['slug', 'updated_at'])
            ->each(fn ($article) => $urls->push([
                'loc' => url('/news/'.$article->slug),
                'lastmod' => $article->updated_at,
                'priority' => 0.5,
            ]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $url) {
            $xml .= '  <url><loc>'.e($url['loc']).'</loc>'
                .($url['lastmod'] ? '<lastmod>'.$url['lastmod']->toAtomString().'</lastmod>' : '')
                .'<priority>'.number_format($url['priority'], 1).'</priority></url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function isMainSite(Request $request): bool
    {
        return ! in_array(strtolower(explode('.', $request->getHost())[0]), ['training', 'booking'], true);
    }
}
