<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Post::class);
<<<<<<< HEAD
            $table->timestamp('created_at')->useCurrent();
=======
            $table->timestamp('created_at');
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
            
            $table->unique(['user_id', 'post_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('likes');
    }
};
