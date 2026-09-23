<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->foreignId('document_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('inactive');
            $table->timestamps();

            $table->unique(['module_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_versions');
    }
};
