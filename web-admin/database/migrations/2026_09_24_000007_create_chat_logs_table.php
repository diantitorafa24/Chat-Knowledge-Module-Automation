<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_user_id')->nullable();
            $table->string('username')->nullable();
            $table->bigInteger('group_id')->nullable();
            $table->text('question');
            $table->longText('answer')->nullable();
            $table->foreignId('module_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_answered')->default(true);
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};
