<?php

declare(strict_types=1);

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('hidden_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Post::class);

            $table->unique(['user_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hidden_posts');
    }
};
