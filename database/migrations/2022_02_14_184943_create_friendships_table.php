<?php

declare(strict_types=1);

use App\Enums\FriendshipStatus;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(User::class, 'friend_id');
            $table->enum('status', [
                FriendshipStatus::CONFIRMED->value,
                FriendshipStatus::PENDING->value,
                FriendshipStatus::BLOCKED->value,
            ]);
            $table->timestamps();

            $table->unique(['user_id', 'friend_id']);
            $table->unique(['friend_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
