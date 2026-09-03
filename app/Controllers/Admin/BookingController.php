<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Client;
use App\Support\Activity;

class BookingController extends Controller
{
    public function index(): void
    {
        $status = (string) request('status', '');
        $search = (string) request('q', '');

        $this->admin('admin.bookings.index', [
            'pageTitle'    => 'Bookings',
            'pageSubtitle' => 'Shoot requests, confirmed dates and quotes',
            'actionLabel'  => 'Add Booking',
            'actionUrl'    => url('/admin/bookings/create'),
            'activeNav'    => 'bookings',
            'bookings'     => Booking::filtered($status, $search),
            'stats'        => Booking::stats(),
            'status'       => $status,
            'search'       => $search,
        ]);
    }

    public function create(): void
    {
        $this->admin('admin.bookings.form', [
            'pageTitle'    => 'New Booking',
            'pageSubtitle' => 'Create a booking manually',
            'actionLabel'  => 'Back to Bookings',
            'actionUrl'    => url('/admin/bookings'),
            'activeNav'    => 'bookings',
            'types'        => Booking::TYPES,
            'clients'      => Client::all(),
            'nextCode'     => Booking::nextCode(),
        ]);
    }

    public function store(): void
    {
        $this->postGuard();

        $name = (string) input('client_name');
        if ($name === '') {
            flash('error', 'A booking needs a client name.');
            redirect('/admin/bookings/create');
        }

        $clientId = Client::findOrCreate($name, (string) input('organisation'), (string) input('email'), (string) input('phone'));

        $id = Booking::create([
            'client_id'    => $clientId,
            'client_name'  => $name,
            'organisation' => (string) input('organisation'),
            'email'        => (string) input('email'),
            'phone'        => (string) input('phone'),
            'project_type' => (string) input('project_type', 'Documentary'),
            'shoot_date'   => $this->dateOrNull('shoot_date'),
            'shoot_days'   => max(1, (int) input('shoot_days', 1)),
            'location'     => (string) input('location'),
            'brief'        => (string) input('brief'),
            'budget'       => (float) str_replace(',', '', (string) input('budget', '0')),
            'status'       => in_array(input('status'), Booking::STATUSES, true) ? (string) input('status') : 'pending',
            'source'       => 'admin',
        ]);

        Booking::addEvent($id, 'Booking created', 'Added from the admin panel');
        Activity::log('created booking', 'booking', $id, $name);

        flash('success', 'Booking created.');
        redirect('/admin/bookings/' . $id);
    }

    public function show(string $id): void
    {
        $booking = Booking::find((int) $id);

        if (!$booking) {
            abort(404, 'Booking not found.');
        }

        $this->admin('admin.bookings.show', [
            'pageTitle'    => $booking['code'],
            'pageSubtitle' => $booking['client_name'] . ' · ' . $booking['project_type'],
            'actionLabel'  => 'Back to Bookings',
            'actionUrl'    => url('/admin/bookings'),
            'activeNav'    => 'bookings',
            'booking'      => $booking,
            'package'      => !empty($booking['package_id']) ? Package::find((int) $booking['package_id']) : null,
            'items'        => Booking::items((int) $id),
            'crew'         => Booking::crew((int) $id),
            'events'       => Booking::events((int) $id),
            'statuses'     => Booking::STATUSES,
            'types'        => Booking::TYPES,
        ]);
    }

    public function update(string $id): void
    {
        $this->postGuard();

        $bookingId = (int) $id;
        $booking = Booking::find($bookingId);
        if (!$booking) {
            abort(404, 'Booking not found.');
        }

        Booking::update($bookingId, [
            'client_name'    => (string) input('client_name', $booking['client_name']),
            'organisation'   => (string) input('organisation'),
            'email'          => (string) input('email'),
            'phone'          => (string) input('phone'),
            'project_type'   => (string) input('project_type', $booking['project_type']),
            'shoot_date'     => $this->dateOrNull('shoot_date'),
            'shoot_days'     => max(1, (int) input('shoot_days', 1)),
            'location'       => (string) input('location'),
            'brief'          => (string) input('brief'),
            'budget'         => (float) str_replace(',', '', (string) input('budget', '0')),
            'internal_notes' => (string) input('internal_notes'),
            'status'         => in_array(input('status'), Booking::STATUSES, true) ? (string) input('status') : $booking['status'],
        ]);

        Booking::replaceItems($bookingId, (array) ($_POST['item_label'] ?? []), (array) ($_POST['item_amount'] ?? []));
        Booking::replaceCrew(
            $bookingId,
            (array) ($_POST['crew_person'] ?? []),
            (array) ($_POST['crew_role'] ?? []),
            (array) ($_POST['crew_days'] ?? [])
        );

        Activity::log('updated booking', 'booking', $bookingId, $booking['code']);
        flash('success', 'Booking saved.');
        redirect('/admin/bookings/' . $bookingId);
    }

    public function setStatus(string $id): void
    {
        $this->postGuard();

        $bookingId = (int) $id;
        $booking = Booking::find($bookingId);
        if (!$booking) {
            abort(404, 'Booking not found.');
        }

        $status = (string) input('status', 'pending');
        if (!in_array($status, Booking::STATUSES, true)) {
            $status = 'pending';
        }

        Booking::update($bookingId, ['status' => $status]);
        Booking::addEvent($bookingId, ucfirst($status), 'Status changed by ' . (\App\Support\Auth::user()['name'] ?? 'admin'));
        Activity::log($status . ' booking', 'booking', $bookingId, $booking['code']);

        flash('success', 'Booking marked as ' . $status . '.');
        redirect('/admin/bookings/' . $bookingId);
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        $booking = Booking::find((int) $id);
        if ($booking) {
            Booking::delete((int) $id);
            Activity::log('deleted booking', 'booking', (int) $id, $booking['code']);
            flash('success', 'Booking deleted.');
        }

        redirect('/admin/bookings');
    }
}
