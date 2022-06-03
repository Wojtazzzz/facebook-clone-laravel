<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(User::class, 'friend_id');
            $table->enum('status', ['PENDING', 'CONFIRMED', 'BLOCKED']);
            $table->timestamps();

            $table->unique(['user_id', 'friend_id']);
<<<<<<< HEAD
            $table->unique(['friend_id', 'user_id']);
=======
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
        });
    }

    public function down()
    {
        Schema::dropIfExists('friendships');
    }
};