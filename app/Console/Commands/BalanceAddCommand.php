<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Console\Command;

class BalanceAddCommand extends Command
{
    protected $signature = 'balance:add 
                            {email : Email пользователя}
                            {amount : Сумма для пополнения}
                            {--description= : Описание транзакции}';
    
    protected $description = 'Пополнить баланс пользователя';

    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->firstOrFail();
        
        $balance = Balance::firstOrCreate(
            ['user_id' => $user->id], 
            ['amount' => 0]
        );
        
        $balance->increment('amount', $this->argument('amount'));
        
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $this->argument('amount'),
            'type' => 'credit',
            'description' => $this->option('description') ?? 'Пополнение баланса',
        ]);

        $this->info("На баланс пользователя {$user->name} добавлено {$this->argument('amount')}. Текущий баланс: {$balance->amount}");
    }
}