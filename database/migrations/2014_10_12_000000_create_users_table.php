<?php

declare(strict_types=1);

use App\Enums\MaritalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->useCurrent();
            $table->string('password');
            $table->string('profile_image');
            $table->string('background_image');
            $table->string('works_at')->nullable();
            $table->string('went_to')->nullable();
            $table->string('lives_in')->nullable();
            $table->string('from')->nullable();
            $table->enum('marital_status', [
                MaritalStatus::SINGLE->value,
                MaritalStatus::IN_A_RELATIONSHIP->value,
                MaritalStatus::ENGAGED->value,
                MaritalStatus::MARRIED->value,
                MaritalStatus::IN_A_CIVIL_PARTNERSHIP->value,
                MaritalStatus::IN_A_DOMESTIC_PARTNERSHIP->value,
                MaritalStatus::IN_AN_OPEN_RELATIONSHIP->value,
                MaritalStatus::ITS_COMPLICATED->value,
                MaritalStatus::SEPARATED->value,
                MaritalStatus::WIDOWED->value,
            ])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
