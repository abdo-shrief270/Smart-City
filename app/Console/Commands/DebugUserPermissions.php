<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DebugUserPermissions extends Command
{
    protected $signature = 'user:permissions {email : User email to inspect} {--ability=ViewAny:GateLog : Specific ability to test}';

    protected $description = 'Inspect a user roles, permissions, and a specific ability outcome';

    public function handle(): int
    {
        $email = $this->argument('email');
        $ability = $this->option('ability');

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("No user with email {$email}");
            return self::FAILURE;
        }

        $this->info("User: {$user->name} <{$user->email}> (id={$user->id})");

        $this->line('');
        $this->line('Roles:');
        foreach ($user->roles()->pluck('name') as $r) {
            $this->line("  - {$r}");
        }

        $this->line('');
        $this->line('Direct permissions (assigned to user):');
        foreach ($user->getDirectPermissions()->pluck('name') as $p) {
            $this->line("  - {$p}");
        }

        $this->line('');
        $this->line('Via-role permissions (inherited from roles):');
        foreach ($user->getPermissionsViaRoles()->pluck('name') as $p) {
            $this->line("  - {$p}");
        }

        $this->line('');
        $this->line("Ability test: \${'user'}->can('{$ability}')");
        $outcome = $user->can($ability);
        $this->{$outcome ? 'warn' : 'info'}('  → ' . ($outcome ? 'TRUE (user can)' : 'FALSE (blocked)'));

        if ($outcome) {
            $superName = config('filament-shield.super_admin.name', 'super_admin');
            $this->line('');
            $this->line('Diagnosing why TRUE:');
            $this->line('  hasRole(super_admin) = ' . ($user->hasRole($superName) ? 'YES — that is why' : 'no'));
            $allPermsHaveIt = $user->getAllPermissions()->contains('name', $ability);
            $this->line('  getAllPermissions() contains it = ' . ($allPermsHaveIt ? 'YES' : 'no'));
            $this->line('');
            $this->warn('If the user is NOT super_admin but still can, run `php artisan permission:cache-reset` to clear stale cache.');
        }

        return self::SUCCESS;
    }
}
