<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Support\Activity;

class PackageController extends Controller
{
    public function index(): void
    {
        $editId = (int) request('edit', 0);

        $this->admin('admin.packages.index', [
            'pageTitle'    => 'Packages',
            'pageSubtitle' => 'Production tiers shown on the services page',
            'actionLabel'  => 'View Services',
            'actionUrl'    => url('/services'),
            'activeNav'    => 'packages',
            'packages'     => Package::all(),
            'editing'      => $editId > 0 ? Package::find($editId) : null,
            'types'        => Booking::TYPES,
        ]);
    }

    public function store(): void
    {
        $this->postGuard();

        $name = trim((string) input('name'));
        if ($name === '') {
            flash('error', 'A package needs a name.');
            redirect('/admin/packages');
        }

        $id = Package::create($this->payload($name));
        Activity::log('added package', 'package', $id, $name);

        flash('success', 'Package added.');
        redirect('/admin/packages');
    }

    public function update(string $id): void
    {
        $this->postGuard();

        $packageId = (int) $id;
        $existing = Package::find($packageId);
        if (!$existing) {
            abort(404, 'Package not found.');
        }

        $name = trim((string) input('name'));
        Package::update($packageId, $this->payload($name, $packageId));
        Activity::log('updated package', 'package', $packageId, $name);

        flash('success', 'Package saved.');
        redirect('/admin/packages');
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        Package::delete((int) $id);
        flash('success', 'Package removed.');

        redirect('/admin/packages');
    }

    private function payload(string $name, ?int $ignoreId = null): array
    {
        $slug = trim((string) input('slug'));
        if ($slug === '') {
            $slug = Package::uniqueSlug($name, $ignoreId);
        } else {
            $slug = slugify($slug);
        }

        return [
            'name'         => $name,
            'slug'         => $slug,
            'tagline'      => trim((string) input('tagline')),
            'description'  => trim((string) input('description')),
            'price_from'   => (float) str_replace(',', '', (string) input('price_from', '0')),
            'price_label'  => trim((string) input('price_label', 'From')) ?: 'From',
            'currency'     => strtoupper(trim((string) input('currency', 'BDT')) ?: 'BDT'),
            'features'     => input('feature', []),
            'service_type' => trim((string) input('service_type')),
            'sort_order'   => (int) input('sort_order', 0),
            'is_featured'  => $this->checkbox('is_featured'),
            'is_active'    => $this->checkbox('is_active'),
            'cta_label'    => trim((string) input('cta_label', 'Enquire')) ?: 'Enquire',
        ];
    }
}
