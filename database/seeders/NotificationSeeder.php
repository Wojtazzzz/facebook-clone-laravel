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
            'first_user' => $initiator->id,
            'second_user' => $invited->id,
            'acted_user' => $initiator->id,
            'status' => 'pending'
        ]);

        $invited->notify(new FriendshipInvitationSended($initiator));
    }
}
