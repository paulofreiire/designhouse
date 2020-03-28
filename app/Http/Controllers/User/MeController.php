<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MeController extends Controller
{
    public function getMe()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->created_at_human = $user->created_at->diffForHumans();
            return new UserResource($user);
            /*return response()->json(["User" => Auth::user()], 200);*/
        }

        return response()->json(null, 401);
    }
}
