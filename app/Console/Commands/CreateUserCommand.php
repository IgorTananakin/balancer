<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Balance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create 
                            {name : User name}
                            {email : User email}
                            {password : User password}
                            {--balance=0 : Initial balance}';
    
    protected $description = 'Create a new user with optional initial balance';

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

        $this->info("User {$user->name} created successfully with balance: {$this->option('balance')}!");
    }
}