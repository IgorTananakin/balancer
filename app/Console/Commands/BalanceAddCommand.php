<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Console\Command;

class BalanceAddCommand extends Command
{
    protected $signature = 'balance:add 
                            {email : User email}
                            {amount : Amount to add}
                            {--description= : Transaction description}';
    
    protected $description = 'Add amount to user balance';

    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->firstOrFail();
        
        $balance = Balance::firstOrCreate(['user_id' => $user->id], ['amount' => 0]);
        $balance->increment('amount', $this->argument('amount'));
        
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $this->argument('amount'),
            'type' => 'credit',
            'description' => $this->option('description') ?? 'Balance top up',
        ]);

        $this->info("Added {$this->argument('amount')} to {$user->name}'s balance. New balance: {$balance->amount}");
    }
}