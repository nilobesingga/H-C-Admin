<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateAdminToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:token
                            {--email= : The email of the admin user}
                            {--name=admin-api-token : The name of the token}
                            {--expiry=365 : Number of days until token expires}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an API token for an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $tokenName = $this->option('name');
        $expiryDays = (int) $this->option('expiry');

        // If email is not provided, ask for it
        if (!$email) {
            $email = $this->ask('Enter the email of the admin user');
        }

        // Find the admin user
        $admin = User::where('email', $email)->first();

        if (!$admin) {
            $this->error("User with email {$email} not found.");

            // Ask if they want to create a new admin user
            if ($this->confirm('Would you like to create a new admin user?')) {
                return $this->createAdminUser();
            }

            return 1;
        }

        // Check if the user is an admin
        if (!$admin->is_admin) {
            $this->warn("User {$email} is not an admin.");

            if ($this->confirm('Would you like to make this user an admin?')) {
                $admin->is_admin = true;
                $admin->save();
                $this->info("User {$email} is now an admin.");
            } else {
                $this->error("Operation cancelled. User must be an admin to generate admin tokens.");
                return 1;
            }
        }

        // Generate the token
        if (method_exists($admin, 'createToken')) {
            $token = $admin->createToken(
                $tokenName,
                ['*'],
                Carbon::now()->addDays($expiryDays)
            );

            $this->info("Admin token for {$email} generated successfully!");
            $this->newLine();
            $this->info("Token: " . ($token->plainTextToken ?? 'Token generation failed'));
            $this->info("Expires: " . Carbon::now()->addDays($expiryDays)->format('Y-m-d H:i:s'));
            $this->newLine();
            $this->warn("Please store this token securely, it won't be displayed again.");

            return 0;
        } else {
            $this->error("Failed to generate token. Make sure the User model has the HasApiTokens trait.");
            return 1;
        }
    }

    /**
     * Create a new admin user.
     */
    private function createAdminUser()
    {
        $email = $this->ask('Enter email for the new admin user');
        $username = $this->ask('Enter username for the new admin user');
        $password = $this->secret('Enter password for the new admin user (min 8 chars with mixed case, numbers, symbols)');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long.');
            return 1;
        }

        try {
            // Create admin user
            $admin = User::create([
                'email' => $email,
                'user_name' => $username,
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'bitrix_user_id' => 'admin-' . \Illuminate\Support\Str::random(8),
                'bitrix_contact_id' => 'admin-' . \Illuminate\Support\Str::random(8),
                'access_token' => \Illuminate\Support\Str::random(64),
                'bitrix_active' => 1,
                'is_admin' => true,
                'is_default_password' => false,
                'status' => 'offline',
                'created_by' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->info("Admin user created successfully with email: {$email}");

            // Generate a token for the new admin
            if (method_exists($admin, 'createToken')) {
                $tokenName = $this->option('name');
                $expiryDays = (int) $this->option('expiry');

                $token = $admin->createToken(
                    $tokenName,
                    ['*'],
                    Carbon::now()->addDays($expiryDays)
                );

                $this->info("Admin token generated successfully!");
                $this->newLine();
                $this->info("Token: " . ($token->plainTextToken ?? 'Token generation failed'));
                $this->info("Expires: " . Carbon::now()->addDays($expiryDays)->format('Y-m-d H:i:s'));
                $this->newLine();
                $this->warn("Please store this token securely, it won't be displayed again.");

                return 0;
            } else {
                $this->error("Failed to generate token. Make sure the User model has the HasApiTokens trait.");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error creating admin user: " . $e->getMessage());
            return 1;
        }
    }
}
