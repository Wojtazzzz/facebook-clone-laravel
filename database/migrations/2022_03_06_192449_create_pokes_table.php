<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('pokes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id');
            $table->foreignIdFor(User::class, 'friend_id');
            $table->foreignIdFor(User::class, 'latest_initiator_id');
            $table->integer('count')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'friend_id']);
            $table->unique(['friend_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokes');
    }
};
