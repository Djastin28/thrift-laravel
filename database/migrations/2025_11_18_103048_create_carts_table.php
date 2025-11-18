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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            // user pemilik keranjang
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // item yang dimasukkan ke keranjang
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->onDelete('cascade');

            $table->unsignedInteger('quantity')->default(1);
            $table->string('notes')->nullable();          // catatan (opsional)
            $table->timestamps();

            // supaya 1 user tidak punya duplikat item yg sama di cart
            $table->unique(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
