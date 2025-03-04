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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Khóa ngoại
            $table->string('name'); // Tên file
            $table->string('path'); // Đường dẫn lưu file
            $table->integer('size'); // Kích thước file (KB)
            $table->string('mime_type'); // Loại file
            $table->string('extension'); // Đuôi file (pdf, docx...)
            $table->enum('visibility', ['public', 'private'])->default('public'); // Quyền truy cập
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};