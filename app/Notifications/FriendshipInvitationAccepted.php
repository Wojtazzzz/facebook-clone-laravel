<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FriendshipInvitationAccepted extends Notification
{
    use Queueable;

    public function __construct(
        private User $initiator
    ) {
        //
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        info('Notifiable: '. $notifiable);
        
        return [
            'type' => NotificationType::FRIENDSHIP_INVITATION_ACCEPTED,
            'initiator' => $this->initiator
        ];
    }
}
