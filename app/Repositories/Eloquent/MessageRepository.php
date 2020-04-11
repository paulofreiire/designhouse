<?php


namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\Message;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Contracts\MessageInterface;

class MessageRepository extends BaseRepository implements MessageInterface
{
    public function model()
    {
        return Message::class;
    }

}
