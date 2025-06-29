<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BalanceCheckCommand extends Command
{
    protected $signature = 'balance:check {email : Email пользователя}';
    
    protected $description = 'Проверить баланс пользователя';

    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->firstOrFail();
        $balance = $user->balance;
        
        $this->info("Пользователь: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Баланс: " . ($balance ? $balance->amount : '0'));
    }
}