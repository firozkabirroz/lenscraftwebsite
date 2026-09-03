<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Content;
use App\Models\Video;
use App\Support\Activity;

class ContentController extends Controller
{
    public function index(): void
    {
        $this->admin('admin.content.index', [
            'pageTitle'    => 'Content',
            'pageSubtitle' => 'Everything the public site reads from the database',
            'actionLabel'  => 'View Site',
            'actionUrl'    => url('/'),
            'activeNav'    => 'content',
            'sections'     => Content::sections(),
        ]);
    }

    public function edit(string $id): void
    {
        $section = Content::find((int) $id);

        if (!$section) {
            abort(404, 'Section not found.');
        }

        $this->admin('admin.content.edit', [
            'pageTitle'    => $section['title'],
            'pageSubtitle' => 'Content › ' . ucfirst($section['page']) . ' › ' . $section['title'],
            'actionLabel'  => 'Back to Content',
            'actionUrl'    => url('/admin/content'),
            'activeNav'    => 'content',
            'section'      => $section,
            'fields'       => Content::fieldsFor($section),
            'values'       => Content::decode($section['data']),
            'revisions'    => Content::revisions((int) $id),
            'reels'        => Video::filtered('', '', ''),
        ]);
    }

    public function update(string $id): void
    {
        $this->postGuard();

        $section = Content::find((int) $id);
        if (!$section) {
            abort(404, 'Section not found.');
        }

        $fields = Content::fieldsFor($section);
        $current = Content::decode($section['data']);
        $data = [];

        foreach ($fields as $key => $meta) {
            if (($meta['type'] ?? 'text') === 'repeater') {
                $rows = [];
                $subKeys = array_keys($meta['fields'] ?? []);
                $first = $subKeys[0] ?? 'title';
                $count = count((array) ($_POST[$key . '_' . $first] ?? []));

                for ($i = 0; $i < $count; $i++) {
                    $row = [];
                    $filled = false;
                    foreach ($subKeys as $subKey) {
                        $value = trim((string) ($_POST[$key . '_' . $subKey][$i] ?? ''));
                        $row[$subKey] = $value;
                        $filled = $filled || $value !== '';
                    }
                    if ($filled) {
                        $rows[] = $row;
                    }
                }

                $data[$key] = $rows;
                continue;
            }

            if (($meta['type'] ?? '') === 'toggle') {
                $data[$key] = !empty($_POST[$key]) ? '1' : '0';
                continue;
            }

            $data[$key] = trim((string) ($_POST[$key] ?? ($current[$key] ?? '')));
        }

        // Keep any keys the editor does not expose (e.g. bullets inside services).
        foreach ($current as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = $value;
            }
        }

        Content::save((int) $id, $data, 'Edited ' . $section['title']);
        Activity::log('published section', 'content', (int) $id, $section['title']);

        flash('success', $section['title'] . ' published to the live site.');
        redirect('/admin/content/' . $id . '/edit');
    }

    public function restore(string $id): void
    {
        $this->postGuard();

        $revisionId = (int) input('revision_id', 0);

        if ($revisionId > 0 && Content::restore($revisionId)) {
            Activity::log('restored revision', 'content', (int) $id, 'Revision #' . $revisionId);
            flash('success', 'Earlier version restored.');
        } else {
            flash('error', 'That revision could not be restored.');
        }

        redirect('/admin/content/' . $id . '/edit');
    }
}
