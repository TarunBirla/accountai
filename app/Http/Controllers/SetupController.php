<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use Throwable;

class SetupController extends Controller
{
    /**
     * All-in-One Automated Setup for Live Hosting / Shared Hosting.
     * Executes: Database Auto-Creation, Migrations, Seeders, Storage Link, Cache Clear & Optimization.
     */
    public function install(Request $request)
    {
        return $this->runAll($request);
    }

    public function runAll(Request $request)
    {
        $expectedKey = env('SETUP_KEY', 'ACCOUNT_AI_SECURE_INSTALL_KEY_2026');
        $providedKey = $request->query('key');

        if (!$providedKey || $providedKey !== $expectedKey) {
            return response()->view('setup.dashboard', [
                'success' => false,
                'error' => 'Unauthorized: Invalid or missing setup key. Please supply ?key=' . $expectedKey . ' in the URL.',
                'steps' => [],
            ], 401);
        }

        $lockFile = storage_path('installed.lock');
        $force = $request->boolean('force');

        if (File::exists($lockFile) && !$force) {
            return response()->view('setup.dashboard', [
                'success' => true,
                'alreadyInstalled' => true,
                'message' => 'AccountAI is already installed and protected by storage/installed.lock. To force re-run, append &force=1 to this URL.',
                'steps' => [
                    ['title' => 'System Lock', 'status' => 'locked', 'detail' => 'Installation locked on ' . File::get($lockFile)],
                ],
            ]);
        }

        $steps = [];

        // -------------------------------------------------------------
        // STEP 1: Auto-Create MySQL Database if not existing
        // -------------------------------------------------------------
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE', 'accountai_db');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');

        try {
            $pdo = new PDO("mysql:host={$dbHost};port={$dbPort}", $dbUser, $dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            $steps[] = [
                'title' => 'Database Auto-Creation',
                'status' => 'success',
                'detail' => "MySQL Database `{$dbName}` verified and ready on {$dbHost}:{$dbPort}.",
            ];
        } catch (Throwable $e) {
            $steps[] = [
                'title' => 'Database Auto-Creation',
                'status' => 'warning',
                'detail' => "Could not automatically create database ({$e->getMessage()}). Proceeding with existing database.",
            ];
        }

        // Reconnect Laravel DB
        try {
            DB::purge('mysql');
            DB::reconnect('mysql');
        } catch (Throwable $e) {
            // Ignore if connection string already initialized
        }

        // -------------------------------------------------------------
        // STEP 2: Application Encryption Key
        // -------------------------------------------------------------
        if (empty(env('APP_KEY')) || env('APP_KEY') === 'base64:') {
            try {
                Artisan::call('key:generate', ['--force' => true]);
                $steps[] = [
                    'title' => 'App Key Generation',
                    'status' => 'success',
                    'detail' => 'Generated encryption key: ' . Artisan::output(),
                ];
            } catch (Throwable $e) {
                $steps[] = [
                    'title' => 'App Key Generation',
                    'status' => 'info',
                    'detail' => 'Using existing application key.',
                ];
            }
        } else {
            $steps[] = [
                'title' => 'App Encryption Key',
                'status' => 'success',
                'detail' => 'Valid application key detected.',
            ];
        }

        // -------------------------------------------------------------
        // STEP 3: Run Database Migrations
        // -------------------------------------------------------------
        try {
            Artisan::call('migrate', [
                '--force' => true,
            ]);
            $steps[] = [
                'title' => 'Database Migrations',
                'status' => 'success',
                'detail' => trim(Artisan::output()) ?: 'All database tables created successfully.',
            ];
        } catch (Throwable $e) {
            $steps[] = [
                'title' => 'Database Migrations',
                'status' => 'error',
                'detail' => 'Migration failed: ' . $e->getMessage(),
            ];
            return response()->view('setup.dashboard', [
                'success' => false,
                'error' => 'Migration error occurred. Please verify your DB credentials in .env.',
                'steps' => $steps,
            ], 500);
        }

        // -------------------------------------------------------------
        // STEP 4: Seed Database (Default Admin + 20 Services)
        // -------------------------------------------------------------
        try {
            Artisan::call('db:seed', [
                '--force' => true,
            ]);
            $steps[] = [
                'title' => 'Data Seeders (Admin & Services)',
                'status' => 'success',
                'detail' => 'Created Default Admin (admin@accountai.com) and all 20 UK Accountancy Services (12 Business + 8 Personal).',
            ];
        } catch (Throwable $e) {
            $steps[] = [
                'title' => 'Data Seeders',
                'status' => 'warning',
                'detail' => 'Seeder note: ' . $e->getMessage(),
            ];
        }

        // -------------------------------------------------------------
        // STEP 5: Storage Link
        // -------------------------------------------------------------
        try {
            Artisan::call('storage:link');
            $steps[] = [
                'title' => 'Storage Symlink',
                'status' => 'success',
                'detail' => trim(Artisan::output()) ?: 'Public storage directory linked.',
            ];
        } catch (Throwable $e) {
            $steps[] = [
                'title' => 'Storage Symlink',
                'status' => 'info',
                'detail' => 'Storage link note: ' . $e->getMessage(),
            ];
        }

        // -------------------------------------------------------------
        // STEP 6: Refresh & Clear Caches
        // -------------------------------------------------------------
        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            $steps[] = [
                'title' => 'Caches Flushed & Optimized',
                'status' => 'success',
                'detail' => 'Config, routes, views, and application caches reset.',
            ];
        } catch (Throwable $e) {
            $steps[] = [
                'title' => 'Caches Flushed',
                'status' => 'info',
                'detail' => $e->getMessage(),
            ];
        }

        // -------------------------------------------------------------
        // STEP 7: Create Lock File
        // -------------------------------------------------------------
        File::put($lockFile, now()->toIso8601String() . ' via /setup/run-all');
        $steps[] = [
            'title' => 'Installation Locked',
            'status' => 'success',
            'detail' => 'Created storage/installed.lock for security.',
        ];

        return response()->view('setup.dashboard', [
            'success' => true,
            'steps' => $steps,
            'dbName' => $dbName,
            'dbHost' => $dbHost,
        ]);
    }
}
