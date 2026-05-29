<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_correction_breaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_correction_request_id');
            $table->dateTime('break_start');
            $table->dateTime('break_end')->nullable();
            $table->timestamps();

            $table->foreign('attendance_correction_request_id', 'acr_breaks_request_fk')
                ->references('id')
                ->on('attendance_correction_requests')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_correction_breaks');
    }
};
