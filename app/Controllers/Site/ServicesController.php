<?php

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Models\Content;
use App\Models\Package;

class ServicesController extends Controller
{
    public function index(): void
    {
        $this->site('site.services', [
            'hero'         => Content::get('services', 'hero', ['heading' => 'Services']),
            'services'     => Content::get('services', 'list', ['items' => []]),
            'packageIntro' => Content::get('services', 'packages', [
                'heading'    => 'Production packages',
                'subheading' => 'Clear starting points — every project is scoped to your brief after a discovery call.',
                'enabled'    => '1',
            ]),
            'packages'     => Package::active(),
            'ctaBand'      => Content::get('home', 'cta_band'),
            'title'        => 'Services',
        ]);
    }
}
