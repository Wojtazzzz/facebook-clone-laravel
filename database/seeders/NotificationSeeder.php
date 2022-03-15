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
        $rootUser = User::firstWhere('last_name', 'Witas');

        // Notification for sended invitation for friends
        $initiator = User::factory()->createOne();

        Friendship::create([
            'user_id' => $initiator->id,
            'friend_id' => $rootUser->id,
            'status' => 'PENDING'
        ]);

        $rootUser->notify(new FriendshipInvitationSended($initiator));
        
        // Notification for accepted invitation
        $inivitationReceiver = User::factory()->createOne();
        
        Friendship::create([
            'user_id' => $rootUser->id,
            'friend_id' => $inivitationReceiver->id,
            'status' => 'CONFIRMED'
        ]);

        $rootUser->notify(new FriendshipInvitationSended($inivitationReceiver));
    }
}
