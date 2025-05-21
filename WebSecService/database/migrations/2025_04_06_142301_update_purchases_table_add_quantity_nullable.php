<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'quantity')) {
                $table->integer('quantity')->nullable();
            } else {
                $table->integer('quantity')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'quantity')) {
                $table->integer('quantity')->nullable(false)->change();
            }
        });
    }
};
