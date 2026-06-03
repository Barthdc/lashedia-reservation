<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;

class GoogleCalendarController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Client();

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        $client->addScope('https://www.googleapis.com/auth/calendar.events');
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect()->away($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect('/pesan-sekarang')
                ->with('success', 'Google Calendar gagal terhubung.');
        }

        $client = new Client();

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        $client->addScope('https://www.googleapis.com/auth/calendar.events');
        $client->setAccessType('offline');

        $token = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            return redirect('/pesan-sekarang')
                ->with('success', 'Google Calendar gagal terhubung.');
        }

        file_put_contents(
            storage_path('app/google-calendar-token.json'),
            json_encode($token)
        );

        return redirect('/pesan-sekarang')
            ->with('success', 'Google Calendar berhasil terhubung.');
    }
}
