<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('normal_drugs', function (Blueprint $table) {
            $table->foreignId('type_concoction_id')->after('drug_id')
                    ->constrained()
                    ->onDelete('cascade');

            $table->string('dose')->after('dd')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('normal_drugs', function (Blueprint $table) {
            $table->dropColumn('type_concoction_id');
            $table->dropColumn('dose');
        });
    }
};
