<?php

namespace Database\Seeders;

use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipInvitationSended;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $invited = User::findOrFail(51);
        $initiator = User::findOrFail(1);

        Friendship::create([
            'user_id' => $initiator->id,
            'friend_id' => $invited->id,
            'status' => 'PENDING'
        ]);

        $invited->notify(new FriendshipInvitationSended($initiator));
    }
}
