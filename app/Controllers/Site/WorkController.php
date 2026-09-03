<?php

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Models\Content;
use App\Models\Project;
use App\Models\Video;

class WorkController extends Controller
{
    public function index(): void
    {
        $category = (string) request('category', '');

        $this->site('site.work', [
            'hero'       => Content::get('work', 'hero', ['heading' => 'Work']),
            'projects'   => Project::published($category),
            'categories' => Project::CATEGORIES,
            'active'     => $category,
            'title'      => 'Work',
        ]);
    }

    public function show(string $slug): void
    {
        $project = Project::findBySlug($slug);

        if (!$project) {
            abort(404, 'That project is not published.');
        }

        Project::incrementViews((int) $project['id']);

        $this->site('site.work-show', [
            'project' => $project,
            'gallery' => Project::gallery((int) $project['id']),
            'videos'  => Video::forWorkGrid(4),
            'related' => array_slice(array_filter(
                Project::published($project['category']),
                static fn (array $p): bool => (int) $p['id'] !== (int) $project['id']
            ), 0, 3),
            'title'   => $project['title'],
        ]);
    }
}
