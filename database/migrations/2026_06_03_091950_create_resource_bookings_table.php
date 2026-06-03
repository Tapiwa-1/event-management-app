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
        Schema::create('resource_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->date('booking_date');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status')->default('Reserved');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['resource_id', 'booking_date']);
            $table->index(['event_id', 'booking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_bookings');
    }
};
