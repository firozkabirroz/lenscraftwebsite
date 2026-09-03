<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\Project;
use App\Models\Video;
use App\Support\Activity;

class ProjectController extends Controller
{
    public function index(): void
    {
        $category = (string) request('category', '');
        $search = (string) request('q', '');

        $this->admin('admin.projects.index', [
            'pageTitle'    => 'Projects',
            'pageSubtitle' => 'Portfolio items shown on the public Work page',
            'actionLabel'  => 'Add Project',
            'actionUrl'    => url('/admin/projects/create'),
            'activeNav'    => 'projects',
            'projects'     => Project::filtered($category, '', $search),
            'categories'   => Project::CATEGORIES,
            'active'       => $category,
            'search'       => $search,
        ]);
    }

    public function create(): void
    {
        $this->form(null);
    }

    public function edit(string $id): void
    {
        $project = Project::find((int) $id);

        if (!$project) {
            abort(404, 'Project not found.');
        }

        $this->form($project);
    }

    private function form(?array $project): void
    {
        $this->admin('admin.projects.form', [
            'pageTitle'    => $project ? 'Edit Project' : 'New Project',
            'pageSubtitle' => $project ? $project['title'] . ' · ' . $project['category'] : 'Create a portfolio item',
            'actionLabel'  => 'Back to Projects',
            'actionUrl'    => url('/admin/projects'),
            'activeNav'    => 'projects',
            'project'      => $project,
            'categories'   => Project::CATEGORIES,
            'images'       => MediaAsset::images(),
            'videos'       => Video::filtered(),
            'gallery'      => $project ? array_column(Project::gallery((int) $project['id']), 'id') : [],
        ]);
    }

    public function store(): void
    {
        $this->postGuard();

        $data = $this->payload();
        if ($data['title'] === '') {
            flash('error', 'A project needs a title.');
            redirect('/admin/projects/create');
        }

        $id = Project::create($data);
        Project::setGallery($id, (array) ($_POST['gallery'] ?? []));
        Activity::log('created project', 'project', $id, $data['title']);

        flash('success', 'Project created.');
        redirect('/admin/projects/' . $id . '/edit');
    }

    public function update(string $id): void
    {
        $this->postGuard();

        $projectId = (int) $id;
        if (!Project::find($projectId)) {
            abort(404, 'Project not found.');
        }

        $data = $this->payload();
        Project::update($projectId, $data);
        Project::setGallery($projectId, (array) ($_POST['gallery'] ?? []));
        Activity::log('edited project', 'project', $projectId, $data['title']);

        flash('success', 'Project saved.');
        redirect('/admin/projects/' . $projectId . '/edit');
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        $project = Project::find((int) $id);
        if ($project) {
            Project::delete((int) $id);
            Activity::log('deleted project', 'project', (int) $id, $project['title']);
            flash('success', 'Project deleted.');
        }

        redirect('/admin/projects');
    }

    private function payload(): array
    {
        return [
            'title'            => (string) input('title'),
            'slug'             => (string) input('slug'),
            'category'         => (string) input('category', 'Documentary'),
            'client_name'      => (string) input('client_name'),
            'year'             => input('year') !== '' ? (int) input('year') : null,
            'summary'          => (string) input('summary'),
            'description'      => (string) input('description'),
            'hero_video_url'   => (string) input('hero_video_url'),
            'video_id'         => $this->intOrNull('video_id'),
            'cover_media_id'   => $this->intOrNull('cover_media_id'),
            'status'           => in_array(input('status'), ['draft', 'scheduled', 'published'], true) ? (string) input('status') : 'draft',
            'show_on_homepage' => $this->checkbox('show_on_homepage'),
            'featured_in_reel' => $this->checkbox('featured_in_reel'),
            'sort_order'       => (int) input('sort_order', 0),
            'meta_title'       => (string) input('meta_title'),
            'meta_description' => (string) input('meta_description'),
        ];
    }
}
