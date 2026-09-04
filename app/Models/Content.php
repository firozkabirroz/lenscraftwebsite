<?php

namespace App\Models;

use App\Support\Auth;
use App\Support\Database;

class Content
{
    /** Section field definitions used by the admin editor. */
    public const FIELDS = [
        'home.hero' => [
            'brand_line'       => ['label' => 'Brand line', 'type' => 'text'],
            'tagline'          => ['label' => 'Tagline', 'type' => 'text'],
            'intro'            => ['label' => 'Intro paragraph', 'type' => 'textarea'],
            'primary_cta'      => ['label' => 'Primary CTA', 'type' => 'text'],
            'primary_link'     => ['label' => 'Primary CTA link', 'type' => 'text'],
            'secondary_cta'    => ['label' => 'Secondary CTA', 'type' => 'text'],
            'background_mode'  => ['label' => 'Background', 'type' => 'select', 'options' => ['video' => 'Video loop', 'image' => 'Still image', 'gradient' => 'Gradient']],
            'show_scroll_hint' => ['label' => 'Show scroll hint', 'type' => 'toggle'],
            'dim_overlay'      => ['label' => 'Dim overlay on video', 'type' => 'toggle'],
        ],
        'home.brands' => [
            'heading' => ['label' => 'Strip heading', 'type' => 'text'],
            'enabled' => ['label' => 'Show the brand strip', 'type' => 'toggle'],
        ],
        'home.selected_work' => [
            'heading'    => ['label' => 'Heading', 'type' => 'text'],
            'subheading' => ['label' => 'Subheading', 'type' => 'textarea'],
            'limit'      => ['label' => 'Projects to show', 'type' => 'text'],
        ],
        'home.disciplines' => [
            'heading' => ['label' => 'Heading', 'type' => 'text'],
            'items'   => ['label' => 'Disciplines', 'type' => 'repeater', 'fields' => ['title' => 'Title', 'desc' => 'Description']],
        ],
        'home.about_teaser' => [
            'heading' => ['label' => 'Heading', 'type' => 'text'],
            'body'    => ['label' => 'Body', 'type' => 'textarea'],
            'cta'     => ['label' => 'CTA label', 'type' => 'text'],
        ],
        'home.cta_band' => [
            'heading' => ['label' => 'Heading', 'type' => 'text'],
            'body'    => ['label' => 'Body', 'type' => 'textarea'],
            'cta'     => ['label' => 'CTA label', 'type' => 'text'],
        ],
        'work.hero' => [
            'heading'    => ['label' => 'Heading', 'type' => 'text'],
            'subheading' => ['label' => 'Subheading', 'type' => 'textarea'],
        ],
        'services.hero' => [
            'heading'    => ['label' => 'Heading', 'type' => 'text'],
            'subheading' => ['label' => 'Subheading', 'type' => 'textarea'],
        ],
        'services.list' => [
            'items' => ['label' => 'Services', 'type' => 'repeater', 'fields' => ['title' => 'Title', 'desc' => 'Description']],
        ],
        'services.packages' => [
            'heading'    => ['label' => 'Section heading', 'type' => 'text'],
            'subheading' => ['label' => 'Section subheading', 'type' => 'textarea'],
            'enabled'    => ['label' => 'Show packages on services page', 'type' => 'toggle'],
        ],
        'about.hero' => [
            'heading'    => ['label' => 'Heading', 'type' => 'text'],
            'subheading' => ['label' => 'Subheading', 'type' => 'textarea'],
        ],
        'about.vision' => [
            'vision'  => ['label' => 'Vision', 'type' => 'textarea'],
            'mission' => ['label' => 'Mission', 'type' => 'textarea'],
        ],
        'about.approach' => [
            'steps' => ['label' => 'Approach steps', 'type' => 'repeater', 'fields' => ['title' => 'Title', 'desc' => 'Description']],
        ],
        'about.team' => [
            'heading'    => ['label' => 'Section heading', 'type' => 'text'],
            'subheading' => ['label' => 'Section subheading', 'type' => 'textarea'],
            'enabled'    => ['label' => 'Show team on About page', 'type' => 'toggle'],
        ],
        'contact.info' => [
            'heading'    => ['label' => 'Heading', 'type' => 'text'],
            'subheading' => ['label' => 'Subheading', 'type' => 'textarea'],
            'phone'      => ['label' => 'Phone', 'type' => 'text'],
            'email'      => ['label' => 'Email', 'type' => 'text'],
            'address'    => ['label' => 'Address', 'type' => 'text'],
            'hours'      => ['label' => 'Hours', 'type' => 'text'],
        ],
    ];

    public static function sections(): array
    {
        return Database::all('SELECT * FROM content_sections ORDER BY FIELD(page, "home","work","services","about","contact"), id ASC');
    }

    public static function find(int $id): ?array
    {
        return Database::first('SELECT * FROM content_sections WHERE id = ?', [$id]);
    }

    /** Returns the decoded data array for page.section, with an optional fallback. */
    public static function get(string $page, string $key, array $default = []): array
    {
        static $cache = [];
        $cacheKey = $page . '.' . $key;

        if (!array_key_exists($cacheKey, $cache)) {
            $row = Database::first('SELECT data FROM content_sections WHERE page = ? AND section_key = ?', [$page, $key]);
            $cache[$cacheKey] = $row ? (json_decode((string) $row['data'], true) ?: []) : [];
        }

        return $cache[$cacheKey] !== [] ? $cache[$cacheKey] : $default;
    }

    public static function save(int $id, array $data, string $note = ''): void
    {
        $section = self::find($id);
        if (!$section) {
            return;
        }

        Database::insert('content_revisions', [
            'section_id' => $id,
            'data'       => $section['data'],
            'note'       => $note !== '' ? $note : 'Previous version',
            'user_id'    => Auth::id(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Database::update('content_sections', [
            'data'       => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_by' => Auth::id(),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $id]);
    }

    public static function revisions(int $sectionId, int $limit = 8): array
    {
        return Database::all(
            'SELECT r.*, u.name AS user_name FROM content_revisions r LEFT JOIN users u ON u.id = r.user_id
             WHERE r.section_id = ? ORDER BY r.created_at DESC LIMIT ' . max(1, min($limit, 30)),
            [$sectionId]
        );
    }

    public static function restore(int $revisionId): bool
    {
        $revision = Database::first('SELECT * FROM content_revisions WHERE id = ?', [$revisionId]);
        if (!$revision) {
            return false;
        }

        $data = json_decode((string) $revision['data'], true) ?: [];
        self::save((int) $revision['section_id'], $data, 'Restored revision #' . $revisionId);

        return true;
    }

    public static function fieldsFor(array $section): array
    {
        return self::FIELDS[$section['page'] . '.' . $section['section_key']] ?? [];
    }

    public static function decode(?string $json): array
    {
        return json_decode((string) $json, true) ?: [];
    }
}
