<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Brand;
use App\Models\MediaAsset;
use App\Support\Activity;

class BrandController extends Controller
{
    public function index(): void
    {
        $editId = (int) request('edit', 0);

        $this->admin('admin.brands.index', [
            'pageTitle'    => 'Brands',
            'pageSubtitle' => 'Logo strip shown on the homepage',
            'actionLabel'  => 'View Site',
            'actionUrl'    => url('/'),
            'activeNav'    => 'brands',
            'brands'       => Brand::all(),
            'editing'      => $editId > 0 ? Brand::find($editId) : null,
            'images'       => MediaAsset::images(),
        ]);
    }

    public function store(): void
    {
        $this->postGuard();

        $name = (string) input('name');
        if ($name === '') {
            flash('error', 'A brand needs a name.');
            redirect('/admin/brands');
        }

        $id = Brand::create($this->payload($name));
        Activity::log('added brand', 'brand', $id, $name);

        flash('success', 'Brand added to the strip.');
        redirect('/admin/brands');
    }

    public function update(string $id): void
    {
        $this->postGuard();

        $brandId = (int) $id;
        if (!Brand::find($brandId)) {
            abort(404, 'Brand not found.');
        }

        $name = (string) input('name');
        Brand::update($brandId, $this->payload($name));
        Activity::log('updated brand', 'brand', $brandId, $name);

        flash('success', 'Brand saved.');
        redirect('/admin/brands');
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        Brand::delete((int) $id);
        flash('success', 'Brand removed.');

        redirect('/admin/brands');
    }

    public function move(string $id): void
    {
        $this->postGuard();
        Brand::move((int) $id, (string) input('direction', 'down'));
        redirect('/admin/brands');
    }

    private function payload(string $name): array
    {
        return [
            'name'          => $name,
            'website'       => (string) input('website'),
            'logo_media_id' => $this->intOrNull('logo_media_id'),
            'sort_order'    => (int) input('sort_order', 0),
            'is_active'     => $this->checkbox('is_active'),
        ];
    }
}
