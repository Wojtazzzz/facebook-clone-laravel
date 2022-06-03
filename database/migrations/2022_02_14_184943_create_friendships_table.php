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
            $table->unique(['friend_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('friendships');
    }
};