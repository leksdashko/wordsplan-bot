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
        Schema::create('vocabulary', function (Blueprint $table) {
					$table->id();

					$table->unsignedBigInteger('telegraph_chat_id');
    			$table->foreign('telegraph_chat_id')->references('id')->on('telegraph_chats');

					$table->string('word');
					$table->string('translation');
					
					$table->boolean('is_learned')->default(false);

					$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vocabulary');
    }
};
