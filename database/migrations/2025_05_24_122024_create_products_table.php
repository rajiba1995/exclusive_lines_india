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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('collection_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subheading')->nullable();
            $table->decimal('mrp', 10, 2)->default(0);
            $table->boolean('new_arrival')->default(false);
            $table->boolean('best_seller')->default(false);
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->string('sku')->unique();
            $table->integer('quantity')->default(0);
            $table->string('brochure')->nullable();
            $table->string('image')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->string('badge')->nullable();
            $table->integer('sequence')->default(0);
            $table->enum('status', [1, 0])->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
