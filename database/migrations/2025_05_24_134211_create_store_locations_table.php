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
        Schema::create('store_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->text('contact_numbers')->nullable(); // Store as comma-separated or JSON
            $table->json('operating_time')->nullable();  // Store as JSON
            $table->text('brands')->nullable();          // Store as comma-separated
            $table->enum('outlet_type', ['Multi Brand Outlets', 'Exclusive Brand Outlets']);
            $table->string('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_locations');
    }
};
