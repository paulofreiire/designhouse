<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(InvitationInterface $invitations, TeamInterface $teams, UserInterface $user)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $user;
    }

    public function invite(Request $request, $teamId)
    {
        $team = $this->teams->find($teamId);

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = auth()->user();

        if (!$user->isOwnerOfTeam($team))
            return response()->json([
                'message' => 'You are not the team owner'
            ], 401);

        if ($team->hasPendingInvite($request->email))
            return response()->json([
                'message' => 'Email already has a pending invitation'
            ], 422);

        $recipient = $this->users->findByEmail($request->email);

        if (!$recipient) {
            $this->createInvitation(false, $team, $request->email);

            return response()->json([
                'message' => 'Invitation sent to user'
            ], 200);
        }

        if ($team->hasUser($recipient)) return response()->json([
            'message' => 'Thsi user seems to be a team member'
        ], 422);

        $this->createInvitation(true, $team, $request->email);
        return response()->json([
            'message' => 'Invitation sent to user'
        ], 200);
    }

    public function resend()
    {

    }

    public function respond()
    {

    }

    public function destroy()
    {

    }

    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime()))
        ]);
        Mail::to($email)
            ->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }
}
