<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Support\Activity;

class ClientController extends Controller
{
    public function index(): void
    {
        $search = (string) request('q', '');
        $editId = (int) request('edit', 0);

        $this->admin('admin.clients.index', [
            'pageTitle'    => 'Clients',
            'pageSubtitle' => 'People and brands the studio works with',
            'actionLabel'  => 'Add Client',
            'actionUrl'    => '#client-form',
            'activeNav'    => 'clients',
            'clients'      => Client::all($search),
            'editing'      => $editId > 0 ? Client::find($editId) : null,
            'bookings'     => Booking::filtered(),
            'search'       => $search,
        ]);
    }

    public function store(): void
    {
        $this->postGuard();

        $name = (string) input('name');
        if ($name === '') {
            flash('error', 'A client needs a name.');
            redirect('/admin/clients');
        }

        $id = Client::create([
            'name'         => $name,
            'organisation' => (string) input('organisation'),
            'email'        => (string) input('email'),
            'phone'        => (string) input('phone'),
            'notes'        => (string) input('notes'),
        ]);

        Activity::log('added client', 'client', $id, $name);
        flash('success', 'Client added.');

        redirect('/admin/clients');
    }

    public function update(string $id): void
    {
        $this->postGuard();

        Client::update((int) $id, [
            'name'         => (string) input('name'),
            'organisation' => (string) input('organisation'),
            'email'        => (string) input('email'),
            'phone'        => (string) input('phone'),
            'notes'        => (string) input('notes'),
        ]);

        Activity::log('updated client', 'client', (int) $id, (string) input('name'));
        flash('success', 'Client updated.');

        redirect('/admin/clients');
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        Client::delete((int) $id);
        flash('success', 'Client removed.');

        redirect('/admin/clients');
    }
}
