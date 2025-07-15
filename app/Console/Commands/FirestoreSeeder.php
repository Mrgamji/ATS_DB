<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Cloud\Firestore\FirestoreClient;

class FirestoreSeeder extends Command
{
    protected $signature = 'firestore:seed';
    protected $description = 'Seed Firestore with HR data';

    public function handle()
    {
        $this->info('Starting Firestore Seeder...');

        try {
            $firestore = new FirestoreClient([
                'keyFilePath' => base_path('firebase.json'),
                'projectId' => env('FIREBASE_PROJECT_ID'),
            ]);
            $this->info('✅ Connected to Firestore.');

            $employees = [
                [
                    'id' => 'EMP001',
                    'first_name' => 'Ahmad',
                    'last_name' => 'Salisu',
                    'email' => 'ahmad@example.com',
                    'designation' => 'Software Engineer',
                    'department' => 'IT',
                    'employment_type' => 'full-time',
                    'manager' => null,
                    'date_of_joining' => '2023-01-01',
                    'date_of_birth' => '1995-05-15',
                    'role' => 'admin',
                    'emergency_contact_name' => 'Maryam Salisu',
                    'emergency_contact_phone' => '08012345678',
                ],
                [
                    'id' => 'EMP002',
                    'first_name' => 'Zainab',
                    'last_name' => 'Abdullahi',
                    'email' => 'zainab@example.com',
                    'designation' => 'HR Officer',
                    'department' => 'HR',
                    'employment_type' => 'intern',
                    'manager' => 'EMP001',
                    'date_of_joining' => '2024-06-01',
                    'date_of_birth' => '1998-09-10',
                    'role' => 'user',
                    'emergency_contact_name' => 'Abdullahi Musa',
                    'emergency_contact_phone' => '08099887766',
                ],
            ];

            $this->info('Seeding employees...');
            foreach ($employees as $emp) {
                try {
                    $emp['created_at'] = now()->toDateTimeString();
                    $emp['updated_at'] = now()->toDateTimeString();

                    $employeeDoc = $firestore->collection('employees')->document($emp['id']);
                    $employeeDoc->set($emp);
                    $this->info("✅ Added employee: {$emp['id']}");

                    $employeeDoc->collection('documents')->add([
                        'type' => 'ID',
                        'file_path' => 'uploads/docs/id_' . $emp['id'] . '.pdf',
                        'created_at' => now()->toDateTimeString(),
                    ]);
                    $this->info("   📄 Added document for {$emp['id']}");

                    $employeeDoc->collection('performance_goals')->add([
                        'goal_title' => 'Improve System Uptime',
                        'description' => 'Maintain 99.9% system availability',
                        'kpi_metric' => 'uptime',
                        'status' => 'in_progress',
                        'start_date' => '2025-01-01',
                        'due_date' => '2025-12-31',
                    ]);
                    $this->info("   🎯 Added performance goal for {$emp['id']}");

                    $employeeDoc->collection('feedback')->add([
                        'reviewer_id' => 'EMP002',
                        'feedback_text' => 'Great leadership',
                        'type' => 'manager',
                        'created_at' => now()->toDateTimeString(),
                    ]);
                    $this->info("   💬 Added feedback for {$emp['id']}");
                } catch (\Throwable $e) {
                    $this->error("❌ Error seeding employee {$emp['id']}: " . $e->getMessage());
                }
            }

            // Example: Seeding an announcement
            $this->info('Seeding announcement...');
            try {
                $firestore->collection('announcements')->add([
                    'title' => 'Welcome to the Company!',
                    'body' => 'We are excited to have new team members join us.',
                    'created_at' => now()->toDateTimeString(),
                    'author_id' => 'EMP001',
                ]);
                $this->info("✅ Added announcement.");
            } catch (\Throwable $e) {
                $this->error('❌ Error seeding announcement: ' . $e->getMessage());
            }

        } catch (\Throwable $e) {
            $this->error('❌ Firestore error: ' . $e->getMessage());
            return;
        }
    }
}