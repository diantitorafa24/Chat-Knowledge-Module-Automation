<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('image_index');       // urutan gambar di dokumen
            $table->string('storage_path');               // path di Laravel storage
            $table->string('mime_type')->default('image/png');
            $table->text('description')->nullable();       // deskripsi dari Gemini Vision
            $table->string('section')->nullable();         // section heading terdekat
            $table->boolean('is_active')->default(false); // ikut aktif/nonaktif bersama versi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_images');
    }
};
