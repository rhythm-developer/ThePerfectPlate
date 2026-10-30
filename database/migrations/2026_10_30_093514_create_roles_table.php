<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        $now = now();

        DB::table('roles')->insert([
            ['name' => 'admin', 'label' => 'Admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer', 'label' => 'Customer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'employee', 'label' => 'Employee', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
