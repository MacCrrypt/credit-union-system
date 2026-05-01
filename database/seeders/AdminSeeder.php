<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('CENTRAL_ADMIN_EMAIL', 'super.admin@example.com');
        $password = env('CENTRAL_ADMIN_PASSWORD');
        $generatedPassword = false;

        if (blank($password)) {
            if (app()->environment('production')) {
                throw new RuntimeException('Set CENTRAL_ADMIN_PASSWORD before seeding production.');
            }

            // Local development can bootstrap with a generated password, but we
            // avoid shipping a predictable default credential into real environments.
            $password = Str::password(20);
            $generatedPassword = true;
        }

        if (! User::where('email', $email)->exists()) {
            User::factory()->create([
                'name' => 'Super Admin',
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'role' => 'central_admin',
            ]);
            $this->command->info("Super Admin user created with email: {$email}");

            if ($generatedPassword) {
                $this->command->warn("Initial central admin password: {$password}");
            }

            return;
        }

        $this->command->info('Super Admin user already exists. Skipping creation.');
    }
}
