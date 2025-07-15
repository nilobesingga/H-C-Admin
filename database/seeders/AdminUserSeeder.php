<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@hensleycook.com')->first();

        if ($existingAdmin) {
            $this->command->info('Admin user already exists. Generating a new token...');
            $admin = $existingAdmin;
        } else {
            // Create admin user
            $admin = User::create([
                'email' => 'admin@hensleycook.com',
                'user_name' => 'admin',
                'password' => Hash::make('Admin@123456'),
                'bitrix_user_id' => 'admin-' . Str::random(8),
                'bitrix_contact_id' => 'admin-' . Str::random(8),
                'access_token' => Str::random(64),
                'bitrix_active' => 1,
                'is_admin' => true,
                'is_default_password' => false,
                'status' => 'offline',
                'created_by' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->command->info('Admin user created with email: admin@hensleycook.com and password: Admin@123456');
        }

        // Generate an admin token for API integration
        if ($admin && method_exists($admin, 'createToken')) {
            $token = $admin->createToken(
                'admin-api-integration',
                ['*'],
                Carbon::now()->addYear()
            );

            // Display the token
            $this->command->info('Admin user created with email: admin@hensley-cook.com and password: Admin@123456');
            $this->command->info('Admin API token: ' . ($token->plainTextToken ?? 'Token generation failed'));
        } else {
            $this->command->error('Failed to generate admin token. Check that the User model has the HasApiTokens trait.');
        }

        //1|988LK2LBV3LApLCXT2HRqN3a9pwVxRdApmGiXES7
    }
}
