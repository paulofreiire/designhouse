<?php

namespace App\Repositories\Contracts;

interface InvitationInterface
{
    public function addUserToTeam($team, $user_id);

    public function removeUserFromTeam($team, $id);
}
