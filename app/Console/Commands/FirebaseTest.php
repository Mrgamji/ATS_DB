<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Cloud\Firestore\FirestoreClient;

class FirebaseTest extends Command
{
    protected $signature = 'firebase:test';
    protected $description = 'Test Firebase Firestore connection';

    public function handle()
    {
        $this->info('Running Firebase test...');

        try {
            $fs = new FirestoreClient([
                'keyFilePath' => base_path('firebase.json'),
                'projectId' => env('FIREBASE_PROJECT_ID'),
            ]);
            
            $fs->collection('test')->add(['ping' => 'ok']);
            $this->info('Good');       
        } catch (\Throwable $e) {
            $this->info('❌ Firebase test failed: ' . $e->getMessage());
        }
    }
}