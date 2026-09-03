<?php

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Models\Brand;
use App\Models\Content;
use App\Models\Project;
use App\Models\Video;

class HomeController extends Controller
{
    public function index(): void
    {
        $selected = Content::get('home', 'selected_work', ['heading' => 'Selected Work', 'limit' => 6]);

        $this->site('site.home', [
            'hero'        => Content::get('home', 'hero'),
            'brandStrip'  => Content::get('home', 'brands', ['heading' => 'Trusted by brands & broadcasters', 'enabled' => '1']),
            'brands'      => Brand::active(),
            'selected'    => $selected,
            'disciplines' => Content::get('home', 'disciplines'),
            'aboutTeaser' => Content::get('home', 'about_teaser'),
            'ctaBand'     => Content::get('home', 'cta_band'),
            'projects'    => Project::homepage((int) ($selected['limit'] ?? 6)),
            'reel'        => Video::heroReel(),
            'title'       => null,
        ]);
    }

    public function trackVideo(string $id): void
    {
        Video::registerView((int) $id);

        json_out(['ok' => true]);
    }
}
