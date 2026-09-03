<?php

namespace App\Controllers\Site;

use App\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Content;
use App\Models\Message;
use App\Models\Package;
use App\Support\Activity;

class ContactController extends Controller
{
    public function index(): void
    {
        $selectedPackage = Package::findBySlug((string) request('package', ''));

        $this->site('site.contact', [
            'info'            => Content::get('contact', 'info', ['heading' => 'Start a project']),
            'types'           => Booking::TYPES,
            'packages'        => Package::active(),
            'selectedPackage' => $selectedPackage,
            'title'           => 'Contact',
        ]);
    }

    public function submit(): void
    {
        verify_csrf();

        $data = [
            'name'         => trim((string) input('name')),
            'email'        => trim((string) input('email')),
            'phone'        => trim((string) input('phone')),
            'organisation' => trim((string) input('organisation')),
            'project_type' => trim((string) input('project_type', 'Documentary')),
            'shoot_date'   => trim((string) input('shoot_date')),
            'location'     => trim((string) input('location')),
            'budget'       => (float) str_replace(',', '', (string) input('budget', '0')),
            'brief'        => trim((string) input('brief')),
            'package_id'   => (int) input('package_id', 0),
        ];

        $package = $data['package_id'] > 0 ? Package::find($data['package_id']) : null;
        if (!$package && ($slug = trim((string) input('package_slug'))) !== '') {
            $package = Package::findBySlug($slug);
        }

        $errors = [];
        if ($data['name'] === '') {
            $errors[] = 'Please tell us your name.';
        }
        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (mb_strlen($data['brief']) < 10) {
            $errors[] = 'Please describe the project in a few words.';
        }
        // Simple honeypot — bots fill hidden fields.
        if (trim((string) input('website')) !== '') {
            $errors[] = 'Submission blocked.';
        }

        if ($errors !== []) {
            remember_old($data);
            foreach ($errors as $error) {
                flash('error', $error);
            }
            redirect('/contact');
        }

        $clientId = Client::findOrCreate($data['name'], $data['organisation'], $data['email'], $data['phone']);

        if ($package) {
            if ($data['budget'] <= 0) {
                $data['budget'] = (float) $package['price_from'];
            }
            if ($package['service_type'] && in_array($package['service_type'], Booking::TYPES, true)) {
                $data['project_type'] = $package['service_type'];
            }
            $data['brief'] = 'Selected package: ' . $package['name'] . "\n\n" . $data['brief'];
        }

        $bookingId = Booking::create([
            'client_id'    => $clientId,
            'client_name'  => $data['name'],
            'organisation' => $data['organisation'],
            'email'        => $data['email'],
            'phone'        => $data['phone'],
            'project_type' => in_array($data['project_type'], Booking::TYPES, true) ? $data['project_type'] : 'Documentary',
            'shoot_date'   => $data['shoot_date'] !== '' ? date('Y-m-d', strtotime($data['shoot_date'])) : null,
            'shoot_days'   => 1,
            'location'     => $data['location'],
            'brief'        => $data['brief'],
            'budget'       => $data['budget'],
            'status'       => 'inquiry',
            'source'       => 'website',
            'package_id'   => $package ? (int) $package['id'] : null,
        ]);

        Booking::addEvent($bookingId, 'Inquiry received', $package ? 'Package: ' . $package['name'] : 'Website contact form');

        if ($package) {
            Booking::replaceItems($bookingId, [$package['name'] . ' package'], [(float) $package['price_from']]);
        }

        Message::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'subject'    => ($package ? $package['name'] . ' — ' : $data['project_type'] . ' — ') . ($data['location'] !== '' ? $data['location'] : 'new inquiry'),
            'body'       => $data['brief'],
            'status'     => 'unread',
            'booking_id' => $bookingId,
        ]);

        Activity::log('received inquiry', 'booking', $bookingId, $data['name'], null);

        clear_old();
        flash('success', 'Thank you — your brief reached the studio. We usually reply within one working day.');
        redirect('/contact');
    }
}
