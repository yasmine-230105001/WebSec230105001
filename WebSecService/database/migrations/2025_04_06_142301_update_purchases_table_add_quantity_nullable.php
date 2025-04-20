<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Make the quantity column nullable
            $table->integer('quantity')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Revert the change (make quantity not nullable again)
            $table->integer('quantity')->nullable(false)->change();
        });
    }
};
