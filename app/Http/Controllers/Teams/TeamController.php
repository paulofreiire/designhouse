<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\User;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TeamController extends Controller
{

    protected $teams, $users, $invitations;

    public function __construct(TeamInterface $teams, UserInterface $users, InvitationInterface $invitations)
    {
        $this->teams = $teams;
        $this->users = $users;
        $this->invitations = $invitations;
    }

    public function index()
    {

    }

    public function fetchUserTeams()
    {
        $teams = $this->teams->fetchUserTeams();
        return TeamResource::collection($teams);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams, name']
        ]);

        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);


    }

    public function findById($id)
    {
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    public function findBySlug($id)
    {

    }


    public function update(Request $request, $id)
    {
        $team = $this->teams->find($id);
        $this->authorize('update', $team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,' . $id]
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    public function destroy($id)
    {
        $team = $this->teams->find($id);
        $this->authorize('delete', $team);

        $team->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }

    public function removeFromTeam($teamId, $userId)
    {
        $team = $this->teams->find($teamId);
        $user = $this->users->find($userId);

        if ($user->isOwnerOfTeam($team))
            return response()->json([
                'message' => 'You are the team owner'
            ], 401);

        if (!auth()->user()->isOwnerOfTeam($team) && auth()->id() != $user->id)
            return response()->json([
                'message' => 'You can not do this'
            ], 401);

        $this->invitations->removeUserFromTeam($team, $userId);

        return response()->json([
            'message' => 'Removed'
        ], 200);


    }
}
