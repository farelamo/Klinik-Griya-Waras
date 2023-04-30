<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('normal_drugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained();
            $table->foreignId('drug_id')->constrained();
            $table->integer('amount');
            $table->integer('times');
            $table->integer('dd');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('normal_drugs');
    }
};