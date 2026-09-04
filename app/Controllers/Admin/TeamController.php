<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\TeamMember;
use App\Support\Activity;

class TeamController extends Controller
{
    public function index(): void
    {
        $editId = (int) request('edit', 0);

        $this->admin('admin.team.index', [
            'pageTitle'    => 'Team',
            'pageSubtitle' => 'People shown on the About page',
            'actionLabel'  => 'View About',
            'actionUrl'    => url('/about'),
            'activeNav'    => 'team',
            'members'      => TeamMember::all(),
            'editing'      => $editId > 0 ? TeamMember::find($editId) : null,
            'images'       => MediaAsset::images(),
        ]);
    }

    public function store(): void
    {
        $this->postGuard();

        $name = trim((string) input('name'));
        if ($name === '') {
            flash('error', 'A team member needs a name.');
            redirect('/admin/team');
        }

        $id = TeamMember::create($this->payload($name));
        Activity::log('added team member', 'team', $id, $name);

        flash('success', 'Team member added.');
        redirect('/admin/team');
    }

    public function update(string $id): void
    {
        $this->postGuard();

        $memberId = (int) $id;
        if (!TeamMember::find($memberId)) {
            abort(404, 'Team member not found.');
        }

        $name = trim((string) input('name'));
        TeamMember::update($memberId, $this->payload($name));
        Activity::log('updated team member', 'team', $memberId, $name);

        flash('success', 'Team member saved.');
        redirect('/admin/team');
    }

    public function destroy(string $id): void
    {
        $this->postGuard();
        TeamMember::delete((int) $id);
        flash('success', 'Team member removed.');
        redirect('/admin/team');
    }

    public function move(string $id): void
    {
        $this->postGuard();
        TeamMember::move((int) $id, (string) input('direction', 'down'));
        redirect('/admin/team');
    }

    private function payload(string $name): array
    {
        return [
            'name'           => $name,
            'role'           => trim((string) input('role')),
            'bio'            => trim((string) input('bio')),
            'photo_media_id' => $this->intOrNull('photo_media_id'),
            'sort_order'     => (int) input('sort_order', 0),
            'is_active'      => $this->checkbox('is_active'),
        ];
    }
}
