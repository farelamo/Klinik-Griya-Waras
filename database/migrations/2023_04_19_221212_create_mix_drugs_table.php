<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mix_drugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('drug_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_concoction_id')->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->integer('times');
            $table->integer('dd');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mix_drugs');
    }
};