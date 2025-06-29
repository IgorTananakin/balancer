<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Balance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create 
                            {name : Имя пользователя}
                            {email : Email пользователя}
                            {password : Пароль пользователя}
                            {--balance=0 : Начальный баланс}';
    
    protected $description = 'Создать нового пользователя с начальным балансом';

    public function handle()
    {
        $user = User::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'password' => Hash::make($this->argument('password')),
        ]);

        Balance::create([
            'user_id' => $user->id,
            'amount' => $this->option('balance'),
        ]);

        $this->info("Пользователь {$user->name} успешно создан с балансом: {$this->option('balance')}!");
    }
}