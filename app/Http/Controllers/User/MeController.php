<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MeController extends Controller
{
    public function getMe()
    {
        if (Auth::check()) {
            return response()->json(["User" => Auth::user()], 200);
        }

        return response()->json(null, 401);
    }
}
