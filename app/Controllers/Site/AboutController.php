<?php

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Models\Content;
use App\Models\Project;
use App\Models\TeamMember;

class AboutController extends Controller
{
    public function index(): void
    {
        $this->site('site.about', [
            'hero'     => Content::get('about', 'hero', ['heading' => 'About LensCraft']),
            'vision'   => Content::get('about', 'vision'),
            'approach' => Content::get('about', 'approach', ['steps' => []]),
            'teamIntro'=> Content::get('about', 'team', [
                'heading' => 'The crew',
                'subheading' => 'Directors, cinematographers and editors who shoot together.',
                'enabled' => '1',
            ]),
            'team'     => TeamMember::active(),
            'ctaBand'  => Content::get('home', 'cta_band'),
            'projects' => Project::published('', 3),
            'title'    => 'About',
        ]);
    }
}
