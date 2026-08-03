<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUser extends Command
{
    protected $signature = 'user:create
        {name : Nama pengguna}
        {email : Email pengguna}
        {--password= : Password (acak jika tidak diisi)}
        {--role=superadmin : Role pengguna}
        {--branch= : ID atau slug cabang}';

    protected $description = 'Membuat pengguna baru';

    public function handle(): int
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->option('password') ?? Str::password(12);
        $roleName = $this->option('role');
        $branchOption = $this->option('branch');

        $role = Role::where('name', $roleName)->first();
        if (! $role) {
            $this->error("Role '{$roleName}' tidak ditemukan.");
            $this->info('Role tersedia: ' . Role::pluck('name')->implode(', '));
            return Command::FAILURE;
        }

        $branch = null;
        if ($branchOption) {
            $branch = Branch::where('id', $branchOption)
                ->orWhere('slug', $branchOption)
                ->first();
            if (! $branch) {
                $this->error("Cabang '{$branchOption}' tidak ditemukan.");
                return Command::FAILURE;
            }
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Email '{$email}' sudah digunakan.");
            return Command::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role->name,
            'branch_id' => $branch?->id,
        ]);

        $user->roles()->attach($role->id);

        $this->info('Pengguna berhasil dibuat!');
        $this->table(
            ['Nama', 'Email', 'Role', 'Cabang'],
            [[
                $user->name,
                $user->email,
                $role->label,
                $branch?->name ?? '-',
            ]]
        );

        if (! $this->option('password')) {
            $this->warn("Password: {$password}");
        }

        return Command::SUCCESS;
    }
}
