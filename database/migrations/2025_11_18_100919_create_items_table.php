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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')      // pemilik / uploader barang
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('name');           // nama barang
            $table->text('description')->nullable();
            $table->string('category')->nullable();   // baju, celana, tas, dll
            $table->string('size')->nullable();       // S/M/L/XL, free size, dll
            $table->string('condition')->default('used'); // used / like new / new

            $table->decimal('price', 12, 2);  // harga
            $table->integer('stock')->default(1);
            $table->string('image_url')->nullable();  // link foto (kalau ada)

            $table->timestamps();             // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
