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
        // 1. Create base/reference tables first

        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Annual, Sick, Maternity
            $table->text('description')->nullable();
            $table->integer('max_days_per_year');
            $table->boolean('requires_approval')->default(true);
            $table->timestamps();
        });

        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Basic, Bonus, Tax, Pension
            $table->enum('type', ['earning', 'deduction']);
            $table->boolean('is_taxable')->default(false);
            $table->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('payroll_month'); // e.g., "2025-06"
            $table->enum('status', ['pending', 'processed', 'paid'])->default('pending');
            $table->timestamps();
        });

        // 2. Tables that reference employees, leave_types, salary_components, payrolls

        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->integer('total_entitled');   // e.g., 20
            $table->integer('used')->default(0);
            $table->integer('remaining')->default(0);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable(); // Manager or HR ID
            $table->text('reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
        });

        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('component_id');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('component_id')->references('id')->on('salary_components')->onDelete('cascade');
        });

        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('total_earnings', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_pay', 10, 2);
            $table->text('remarks')->nullable(); // Optional notes
            $table->timestamps();

            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        Schema::create('tax_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('payroll_id');
            $table->decimal('gross_income', 10, 2);
            $table->decimal('taxable_income', 10, 2);
            $table->decimal('tax_deducted', 10, 2);
            $table->string('tax_code')->nullable();
            $table->date('statement_date');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade');
        });


        // Documents table
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('type'); // e.g., contract, ID, certificate
            $table->string('file_path');
            $table->timestamps();
        });

        // Performance Goals
        Schema::create('performance_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('goal_title');
            $table->text('description')->nullable();
            $table->string('kpi_metric');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->date('start_date');
            $table->date('due_date');
            $table->timestamps();
        });

        // Performance Reviews
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->enum('period', ['mid_year', 'annual']);
            $table->year('review_year');
            $table->foreignId('reviewer_id')->constrained('employees');
            $table->text('comments')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();
        });

        // Feedback
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('employees')->onDelete('cascade');
            $table->text('feedback_text');
            $table->enum('type', ['peer', 'manager']);
            $table->timestamps();
        });

        // Promotion Recommendations
        Schema::create('promotion_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('recommended_by')->constrained('employees');
            $table->boolean('recommended')->default(false);
            $table->text('justification')->nullable();
            $table->timestamp('recommended_at')->nullable();
            $table->timestamps();
        });

        // Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('external_link')->nullable(); // For external LMS integration
            $table->timestamps();
        });

        // Course Assignments
        Schema::create('course_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->enum('status', ['assigned', 'in_progress', 'completed'])->default('assigned');
            $table->timestamp('assigned_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Certifications
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('certificate_name');
            $table->string('file_path')->nullable();
            $table->date('issued_date');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');
            $table->timestamps();
        });

          // Announcements
          Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->dateTime('published_at');
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal');
            $table->foreignId('announcer_id')->constrained('employees')->onDelete('set null')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // Analytics Logs
        Schema::create('analytics_logs', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // e.g., headcount, attrition, compliance
            $table->foreignId('generated_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->json('filters')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();
        });

        // Reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('visibility', ['hr', 'manager', 'leadership'])->default('hr');
            $table->json('data');
            $table->foreignId('created_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->timestamps();
        });
            // Audit Trails
            Schema::create('audit_trails', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('set null');
                $table->string('action'); // e.g., login, update_profile, delete_record
                $table->string('target_table')->nullable();
                $table->string('target_id')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
    

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('attendance_date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->enum('method', ['facial_recognition', 'biometric', 'remote'])->default('facial_recognition');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->boolean('is_late')->default(false);
            $table->boolean('is_absent')->default(false);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            // Do NOT add shift_id foreign key here unless `shifts` table exists before
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('tax_statements');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('employee_salaries');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('salary_components');
        Schema::dropIfExists('leave_types');
    }
};
