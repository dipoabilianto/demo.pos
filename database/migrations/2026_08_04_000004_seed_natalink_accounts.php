<?php

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $roles = Role::all()->keyBy('name');
        $branchUtama = Branch::where('name', 'Cabang Utama')->first();
        $branchPulung = Branch::where('name', 'Cabang Pulung Kencana')->first();

        $accounts = [
            ['name' => 'Abilianto', 'email' => 'abilianto@gmail.com', 'password' => 'jagur1995', 'role' => 'superadmin', 'branch_id' => null],
            ['name' => 'OWNER', 'email' => 'owner@natalink.id', 'password' => 'owner1234', 'role' => 'owner', 'branch_id' => null],
            ['name' => 'ADMIN 01', 'email' => 'admin01@natalink.id', 'password' => 'owner1234', 'role' => 'admin', 'branch_id' => $branchUtama?->id],
            ['name' => 'ADMIN 02', 'email' => 'admin02@natalink.id', 'password' => 'owner1234', 'role' => 'admin', 'branch_id' => $branchPulung?->id],
            ['name' => 'KASIR 01', 'email' => 'kasir01@natalink.id', 'password' => 'owner1234', 'role' => 'kasir', 'branch_id' => $branchUtama?->id],
            ['name' => 'KASIR 02', 'email' => 'kasir02@natalink.id', 'password' => 'owner1234', 'role' => 'kasir', 'branch_id' => $branchPulung?->id],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(['email' => $account['email']], [
                'name' => $account['name'],
                'password' => bcrypt($account['password']),
                'role' => $account['role'],
                'branch_id' => $account['branch_id'],
            ]);

            if ($user->wasRecentlyCreated && $roles->has($account['role'])) {
                $user->roles()->attach($roles[$account['role']]);
            }
        }
    }

    public function down(): void
    {
        User::whereIn('email', [
            'abilianto@gmail.com',
            'owner@natalink.id',
            'admin01@natalink.id',
            'admin02@natalink.id',
            'kasir01@natalink.id',
            'kasir02@natalink.id',
        ])->each(function (User $user) {
            $user->roles()->detach();
            $user->delete();
        });
    }
};
