<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    public function sendResetResponse(Request $request, $response)
    {
        return response()->json(['status' => trans($response)], 200);
    }

    public function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json(['email' => trans($response)], 200);
    }

}
