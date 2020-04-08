<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $users;
    public function __construct(UserInterface $users)
    {
        $this->users = $users;
    }

    public function index()
    {
        Log::info("teste");
        $users = $this->users->withCriteria([
            new EagerLoad('designs')
        ])->all();
        return UserResource::collection($users);
    }
}
