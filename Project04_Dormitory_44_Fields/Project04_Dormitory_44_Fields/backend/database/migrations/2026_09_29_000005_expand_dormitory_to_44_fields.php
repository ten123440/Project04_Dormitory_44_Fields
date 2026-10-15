<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('building_code', 20)->unique();
            $table->string('name', 100);
            $table->text('address')->nullable();
            $table->unsignedSmallInteger('total_floors');
            $table->string('contact_phone', 20)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_code', 30)->nullable()->unique();
            $table->string('faculty', 150)->nullable();
            $table->string('major', 150)->nullable();
        });
        Schema::table('room_types', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
        });
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('building_id')->nullable()->constrained('buildings')->restrictOnDelete();
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('check_out_date')->nullable();
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropConstrainedForeignId('building_id');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['check_out_date', 'monthly_rate', 'deposit_amount', 'notes', 'created_at']);
        });
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['description', 'capacity', 'deposit_amount']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['student_code']);
            $table->dropColumn(['student_code', 'faculty', 'major']);
        });
        Schema::dropIfExists('buildings');
    }
};
