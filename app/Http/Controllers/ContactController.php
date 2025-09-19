<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;


class ContactController extends Controller
{
    public function send(Request $request)
{
    try {
        $data = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email',
            'phone'   => 'required|string|max:30',
            'message' => 'nullable|string|max:5000',
        ]);

        Mail::to(env('MAIL_ADMIN_TO'))->send(new \App\Mail\ContactMail($data));

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}

}
