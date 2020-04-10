<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'message' => 'This user seems to be a team member'
        ], 422);

        $this->createInvitation(true, $team, $request->email);
        return response()->json([
            'message' => 'Invitation sent to user'
        ], 200);
    }

    public function resend($id)
    {
        $invitation = $this->invitations->find($id);

        $this->authorize('resend', $invitation);
        if (!auth()->user()->isOwnerOfTeam($invitation->team))
            return response()->json([
                'message' => 'You are not the team owner'
            ], 401);
        $recipient = $this->users->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));

        return response()->json([
            'message' => 'Invitation resent'
        ], 200);
    }

    public function respond(Request $request, $id)
    {
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);

        $token = $request->token;
        $decision = $request->decision; //y or n
        $invitation = $this->invitations->find($id);
        Log::info($invitation->recipient_email);

        //check if belongs to user
        $this->authorize('respond', $invitation);

        if ($invitation->token != $token)
            return response()->json([
                'message' => 'This is not your invitation'
            ], 401);

        if ($decision != 'deny')
            $this->invitations->addUserToTeam($invitation->team, auth()->id());

        $invitation->delete();

        return response()->json([
            'message' => 'Successful'
        ], 200);
    }

    public function destroy($id)
    {
        $invitation = $this->invitations->find($id);
        $this->authorize('delete', $invitation);
        $invitation->delete();

        return response()->json([
            'message' => 'Deleted'
        ], 200);
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
