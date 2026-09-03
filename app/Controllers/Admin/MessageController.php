<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Message;
use App\Support\Activity;
use App\Support\Auth;

class MessageController extends Controller
{
    public function index(): void
    {
        $status = (string) request('status', '');
        $search = (string) request('q', '');
        $messages = Message::filtered($status, $search);

        $selectedId = (int) request('id', 0);
        $selected = $selectedId > 0 ? Message::find($selectedId) : ($messages[0] ?? null);

        if ($selected) {
            Message::markRead((int) $selected['id']);
            $selected['status'] = $selected['status'] === 'unread' ? 'read' : $selected['status'];
        }

        $this->admin('admin.messages.index', [
            'pageTitle'    => 'Messages',
            'pageSubtitle' => 'Inquiries from the website contact form',
            'actionLabel'  => 'New Booking',
            'actionUrl'    => url('/admin/bookings/create'),
            'activeNav'    => 'messages',
            'messages'     => $messages,
            'selected'     => $selected,
            'replies'      => $selected ? Message::replies((int) $selected['id']) : [],
            'unread'       => Message::unreadCount(),
            'status'       => $status,
            'search'       => $search,
        ]);
    }

    public function reply(string $id): void
    {
        $this->postGuard();

        $messageId = (int) $id;
        $message = Message::find($messageId);
        if (!$message) {
            abort(404, 'Message not found.');
        }

        $body = (string) input('body');
        if (trim($body) === '') {
            flash('error', 'Write something before sending.');
            redirect('/admin/messages?id=' . $messageId);
        }

        Message::addReply($messageId, Auth::id(), $body);
        Message::setStatus($messageId, 'read');
        Activity::log('replied to', 'message', $messageId, $message['name']);

        // Mail is disabled on most local setups; the reply is stored either way.
        if (config('app_env') === 'production' && $message['email']) {
            @mail(
                $message['email'],
                'Re: ' . $message['subject'],
                $body,
                'From: ' . \App\Support\Settings::get('email', 'hello@lenscraftproduction.com')
            );
        }

        flash('success', 'Reply saved to the thread.');
        redirect('/admin/messages?id=' . $messageId);
    }

    public function setStatus(string $id): void
    {
        $this->postGuard();

        $status = (string) input('status', 'read');
        if (!in_array($status, ['unread', 'read', 'archived'], true)) {
            $status = 'read';
        }

        Message::setStatus((int) $id, $status);
        flash('success', 'Message marked as ' . $status . '.');

        redirect('/admin/messages');
    }

    public function convert(string $id): void
    {
        $this->postGuard();

        $message = Message::find((int) $id);
        if (!$message) {
            abort(404, 'Message not found.');
        }

        if ($message['booking_id']) {
            redirect('/admin/bookings/' . $message['booking_id']);
        }

        $clientId = Client::findOrCreate($message['name'], '', (string) $message['email'], (string) $message['phone']);

        $bookingId = Booking::create([
            'client_id'    => $clientId,
            'client_name'  => $message['name'],
            'email'        => $message['email'],
            'phone'        => $message['phone'],
            'project_type' => 'Documentary',
            'shoot_days'   => 1,
            'brief'        => $message['body'],
            'status'       => 'pending',
            'source'       => 'message',
        ]);

        Booking::addEvent($bookingId, 'Created from message', $message['subject']);
        \App\Support\Database::update('messages', ['booking_id' => $bookingId], 'id = :id', ['id' => (int) $id]);
        Activity::log('created booking from message', 'booking', $bookingId, $message['name']);

        flash('success', 'Booking created from this inquiry.');
        redirect('/admin/bookings/' . $bookingId);
    }

    public function destroy(string $id): void
    {
        $this->postGuard();

        Message::delete((int) $id);
        flash('success', 'Message deleted.');

        redirect('/admin/messages');
    }
}
