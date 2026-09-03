<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\MediaAsset;
use App\Support\Uploader;
use RuntimeException;

class MediaController extends Controller
{
    public function index(): void
    {
        $type = (string) request('type', '');
        $search = (string) request('q', '');

        $this->admin('admin.media.index', [
            'pageTitle'    => 'Media',
            'pageSubtitle' => 'Stills, posters and brand assets',
            'actionLabel'  => 'Upload Media',
            'actionUrl'    => '#upload',
            'activeNav'    => 'media',
            'items'        => MediaAsset::filtered($type, $search),
            'stats'        => MediaAsset::stats(),
            'type'         => $type,
            'search'       => $search,
        ]);
    }

    public function store(): void
    {
        $this->postGuard();

        $files = $_FILES['files'] ?? null;
        if (!$files || !isset($files['name']) || !is_array($files['name'])) {
            flash('error', 'Choose at least one file.');
            redirect('/admin/media');
        }

        $saved = 0;
        $errors = [];

        foreach (array_keys($files['name']) as $i) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            try {
                Uploader::storeImage([
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ]);
                $saved++;
            } catch (RuntimeException $e) {
                $errors[] = $files['name'][$i] . ': ' . $e->getMessage();
            }
        }

        if ($saved > 0) {
            flash('success', $saved . ' file' . ($saved === 1 ? '' : 's') . ' uploaded.');
        }
        foreach ($errors as $error) {
            flash('error', $error);
        }

        redirect('/admin/media');
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        MediaAsset::delete((int) $id);
        flash('success', 'File removed.');

        redirect('/admin/media');
    }
}
