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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            
            // Personal Details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('photo')->nullable(); // URL or path to stored photo
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
        
            // Employment Details
            $table->string('designation');
            $table->string('department');
            $table->unsignedBigInteger('manager_id')->nullable(); // Self-referencing foreign key
            $table->enum('employment_type', ['full-time', 'graduate-trainee', 'intern']);
            $table->date('date_of_joining')->nullable();
            $table->string('employee_code')->unique(); // Custom employee identifier
        
            // Other
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
        
            $table->string('role');
        
            $table->timestamps();
        
            // Foreign keys
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
