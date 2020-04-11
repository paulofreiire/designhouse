<?php


namespace App\Repositories\Eloquent;

use App\Models\Chat;
use App\Repositories\Contracts\ChatInterface;
use Illuminate\Support\Facades\Log;

class ChatRepository extends BaseRepository implements ChatInterface
{
    public function model()
    {
        return Chat::class;
    }

    public function createParticipants($chatId, array $data)
    {
        $chat = $this->model->find($chatId);
        Log::info($data);
        $chat->participants()->sync($data);
    }

    public function getUserChats()
    {
        return auth()->user()->chats()
            ->with(['messages', 'participants'])
            ->get();
    }

}
