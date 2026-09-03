<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\Project;
use App\Models\Video;
use App\Support\Activity;
use App\Support\Auth;
use App\Support\Uploader;
use RuntimeException;

class VideoController extends Controller
{
    public function index(): void
    {
        $status = (string) request('status', '');
        $search = (string) request('q', '');

        $this->admin('admin.videos.index', [
            'pageTitle'    => 'Videos',
            'pageSubtitle' => 'Reels, trailers and campaign films',
            'actionLabel'  => 'Upload Video',
            'actionUrl'    => url('/admin/videos/upload'),
            'activeNav'    => 'videos',
            'videos'       => Video::filtered($status, '', $search),
            'stats'        => Video::stats(),
            'status'       => $status,
            'search'       => $search,
        ]);
    }

    public function uploadForm(): void
    {
        $this->admin('admin.videos.upload', [
            'pageTitle'    => 'Upload Video',
            'pageSubtitle' => 'Local file upload or an external embed link',
            'actionLabel'  => 'Back to Videos',
            'actionUrl'    => url('/admin/videos'),
            'activeNav'    => 'videos',
            'categories'   => Video::CATEGORIES,
            'projects'     => Project::filtered(),
            'chunkSize'    => (int) config('uploads')['chunk_size'],
            'maxVideo'     => (int) config('uploads')['max_video'],
        ]);
    }

    /** XHR endpoint: receives one chunk of a large video file. */
    public function chunk(): void
    {
        Auth::requireLogin();
        verify_csrf();

        try {
            $result = Uploader::receiveChunk(
                (string) ($_POST['upload_id'] ?? ''),
                (int) ($_POST['index'] ?? 0),
                (int) ($_POST['total'] ?? 0),
                (string) ($_POST['name'] ?? 'video.mp4'),
                $_FILES['chunk'] ?? []
            );
        } catch (RuntimeException $e) {
            json_out(['ok' => false, 'error' => $e->getMessage()], 422);
        }

        json_out(['ok' => true] + $result);
    }

    public function store(): void
    {
        $this->postGuard();

        $source = input('source') === 'embed' ? 'embed' : 'local';
        $title = (string) input('title');
        $filePath = (string) input('file_path');
        $embed = (string) input('embed_url');

        if ($title === '') {
            flash('error', 'Give the video a title.');
            redirect('/admin/videos/upload');
        }
        if ($source === 'local' && $filePath === '') {
            flash('error', 'Upload a video file first, or switch to an embed link.');
            redirect('/admin/videos/upload');
        }
        if ($source === 'embed' && $embed === '') {
            flash('error', 'Paste a YouTube or Vimeo link.');
            redirect('/admin/videos/upload');
        }

        $id = Video::create([
            'title'            => $title,
            'category'         => (string) input('category', 'Hero Reel'),
            'source'           => $source,
            'file_path'        => $source === 'local' ? $filePath : null,
            'embed_url'        => $source === 'embed' ? $embed : null,
            'provider'         => $source === 'embed' ? embed_provider($embed) : 'local',
            'poster_media_id'  => $this->intOrNull('poster_media_id'),
            'project_id'       => $this->intOrNull('project_id'),
            'duration_seconds' => (int) input('duration_seconds', 0) ?: null,
            'size_bytes'       => (int) input('size_bytes', 0),
            'status'           => 'ready',
            'is_published'     => $this->checkbox('is_published'),
            'place_home_hero'  => $this->checkbox('place_home_hero'),
            'place_work_grid'  => $this->checkbox('place_work_grid'),
            'place_services'   => $this->checkbox('place_services'),
        ]);

        Activity::log('uploaded', 'video', $id, $title);
        flash('success', 'Video added to the library.');
        redirect('/admin/videos/' . $id . '/edit');
    }

    public function edit(string $id): void
    {
        $video = Video::find((int) $id);

        if (!$video) {
            abort(404, 'Video not found.');
        }

        $this->admin('admin.videos.form', [
            'pageTitle'    => 'Edit Video',
            'pageSubtitle' => $video['title'] . ' · ' . $video['category'],
            'actionLabel'  => 'Back to Videos',
            'actionUrl'    => url('/admin/videos'),
            'activeNav'    => 'videos',
            'video'        => $video,
            'categories'   => Video::CATEGORIES,
            'projects'     => Project::filtered(),
            'images'       => MediaAsset::images(),
        ]);
    }

    public function update(string $id): void
    {
        $this->postGuard();

        $videoId = (int) $id;
        $video = Video::find($videoId);
        if (!$video) {
            abort(404, 'Video not found.');
        }

        $source = input('source') === 'embed' ? 'embed' : 'local';
        $embed = (string) input('embed_url');

        Video::update($videoId, [
            'title'            => (string) input('title', $video['title']),
            'category'         => (string) input('category', $video['category']),
            'source'           => $source,
            'embed_url'        => $source === 'embed' ? $embed : null,
            'provider'         => $source === 'embed' ? embed_provider($embed) : 'local',
            'poster_media_id'  => $this->intOrNull('poster_media_id'),
            'project_id'       => $this->intOrNull('project_id'),
            'duration_seconds' => (int) input('duration_seconds', 0) ?: null,
            'status'           => in_array(input('status'), ['processing', 'ready', 'failed'], true) ? (string) input('status') : 'ready',
            'is_published'     => $this->checkbox('is_published'),
            'place_home_hero'  => $this->checkbox('place_home_hero'),
            'place_work_grid'  => $this->checkbox('place_work_grid'),
            'place_services'   => $this->checkbox('place_services'),
        ]);

        Activity::log('edited video', 'video', $videoId, (string) input('title', $video['title']));
        flash('success', 'Video saved.');
        redirect('/admin/videos/' . $videoId . '/edit');
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        $video = Video::find((int) $id);
        if ($video) {
            Uploader::deleteFile($video['file_path']);
            Video::delete((int) $id);
            Activity::log('deleted', 'video', (int) $id, $video['title']);
            flash('success', 'Video deleted.');
        }

        redirect('/admin/videos');
    }
}
