<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->enum('role', ['superadmin', 'admin', 'doctor', 'patient']);
            $table->enum('gender', ['L', 'P']);
            $table->date('birth');
            $table->text('address');
            $table->string('phone', 13);
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
