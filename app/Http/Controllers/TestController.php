<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MessageStatus;
use App\Models\Message;
use Illuminate\Foundation\Testing\WithFaker;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function __invoke()
    {
        // Message::factory(8)->create([
        //     'sender_id' => 2,
        //     'receiver_id' => 1,
        //     'status' => MessageStatus::READ,
        //     'read_at' => now(),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // return $date->dependentFormat();

        $now = now();
        $day = now()->subDay();
        $wek = now()->subWeek();

        echo "
            {$now->dependentFormat()} </br>
            {$day->dependentFormat()} </br>
            {$wek->dependentFormat()} </br>
        ";
    }
}
