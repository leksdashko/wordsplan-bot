<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

						$table->unsignedBigInteger('telegraph_chat_id')->unique();
    				$table->foreign('telegraph_chat_id')->references('id')->on('telegraph_chats');

						$table->string('mode');
						$table->string('app_lang')->default('en');
						$table->string('user_lang')->default('en');
						$table->string('learning_lang')->default('en');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
