<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BalanceCheckCommand extends Command
{
    protected $signature = 'balance:check {email : User email}';
    
    protected $description = 'Check user balance';

    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->firstOrFail();
        $balance = $user->balance;
        
        $this->info("User: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Balance: " . ($balance ? $balance->amount : '0'));
    }
}