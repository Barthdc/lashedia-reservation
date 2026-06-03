<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Carbon\Carbon;

class GoogleCalendarService
{
    public function createBookingEvent($booking)
    {
        $tokenPath = storage_path('app/google-calendar-token.json');

        if (!file_exists($tokenPath)) {
            return null;
        }

        $client = new Client();

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope('https://www.googleapis.com/auth/calendar.events');
        $client->setAccessType('offline');

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $newToken = $client->fetchAccessTokenWithRefreshToken(
                    $client->getRefreshToken()
                );

                $updatedToken = array_merge($accessToken, $newToken);

                file_put_contents(
                    $tokenPath,
                    json_encode($updatedToken)
                );

                $client->setAccessToken($updatedToken);
            } else {
                return null;
            }
        }

        $service = new Calendar($client);

        /*
        |--------------------------------------------------------------------------
        | JAM RESERVASI SESUAI INPUT WEBSITE
        |--------------------------------------------------------------------------
        | Contoh:
        | date = 2026-06-03
        | time = 10:00
        | hasil dikirim ke Google = 2026-06-03T10:00:00+07:00
        |--------------------------------------------------------------------------
        */

        $tanggal = $booking->date;
        $jam = substr($booking->time, 0, 5);

        $start = Carbon::createFromFormat(
            'Y-m-d H:i',
            $tanggal . ' ' . $jam,
            'Asia/Jakarta'
        );

        $end = $start->copy()->addHours(2);

        $event = new Event([
            'summary' => 'Reservasi Lashedia - ' . $booking->name,

            'description' =>
                "Status: {$booking->status}\n" .
                "Layanan: {$booking->service}\n" .
                "Penata Rias: {$booking->stylist}\n" .
                "Nama Customer: {$booking->name}\n" .
                "Email: {$booking->email}\n" .
                "No WhatsApp: {$booking->phone}\n" .
                "Metode Pembayaran: {$booking->payment_method}\n" .
                "Catatan: {$booking->note}",

            'start' => [
                'dateTime' => $start->format('Y-m-d\TH:i:sP'),
                'timeZone' => 'Asia/Jakarta',
            ],

            'end' => [
                'dateTime' => $end->format('Y-m-d\TH:i:sP'),
                'timeZone' => 'Asia/Jakarta',
            ],
        ]);

        return $service->events->insert(
            env('GOOGLE_CALENDAR_ID', 'primary'),
            $event
        );
    }
}
