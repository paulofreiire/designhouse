<?php

namespace App\Repositories\Contracts;

interface ChatInterface
{
    public function createParticipants($chatId, array $data);

    public function getUserChats();
}
