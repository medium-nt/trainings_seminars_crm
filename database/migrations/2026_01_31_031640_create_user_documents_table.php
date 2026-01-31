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
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // Тип документа: passport_main, passport_reg, snils, diploma_basis и др.
            $table->string('file_path'); // Путь к файлу в storage
            $table->string('file_name'); // Оригинальное имя файла
            $table->string('mime_type'); // image/jpeg, application/pdf
            $table->unsignedInteger('file_size'); // Размер в байтах
            $table->boolean('is_approved')->default(false); // Статус одобрения
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
