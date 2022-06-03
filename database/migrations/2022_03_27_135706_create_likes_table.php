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
            $table->timestamp('created_at')->useCurrent();
            
            $table->unique(['user_id', 'post_id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('likes');
    }
};
