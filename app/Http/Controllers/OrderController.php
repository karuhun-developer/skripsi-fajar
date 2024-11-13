<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function callback(Request $request)
    {
        $serverKey = getenv('MIDTRANS_SERVER_KEY');
        $hashed = hash('sha512', $request->input('order_id') . $request->input('status_code') . $request->input('gross_amount') . $serverKey);

        if ($hashed === $request->input('signature_key')) {
            if ($request->input('status_code') == 200) {

            }
        }
    }
}
