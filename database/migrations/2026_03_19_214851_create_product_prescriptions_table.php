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
        // Pehle check karo table exist karti hai ya nahi
        if (!Schema::hasTable('product_prescriptions')) {
            Schema::create('product_prescriptions', function (Blueprint $table) {
                $table->id();
                
                // Polymorphic relationship - manually
                $table->string('prescriptionable_type');
                $table->unsignedBigInteger('prescriptionable_id');
                
                // Prescription fields
                $table->decimal('right_axis', 5, 2)->nullable();
                $table->decimal('right_spherical', 5, 2)->nullable();
                $table->decimal('right_cylindrical', 5, 2)->nullable();
                $table->decimal('left_axis', 5, 2)->nullable();
                $table->decimal('left_spherical', 5, 2)->nullable();
                $table->decimal('left_cylindrical', 5, 2)->nullable();
                
                // Extra fields
                $table->string('prescription_type')->default('single_vision');
                $table->text('notes')->nullable();
                
                $table->timestamps();
                
                // Indexes with short names
                $table->index(['prescriptionable_type', 'prescriptionable_id'], 'pres_idx');
                $table->index('prescriptionable_type', 'pres_type_idx');
                $table->index('prescriptionable_id', 'pres_id_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prescriptions');
    }
};