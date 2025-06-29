<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BalanceSubtractCommand extends Command
{
    protected $signature = 'balance:subtract 
                            {email : Email пользователя}
                            {amount : Сумма списания (должна быть положительной)}
                            {--description= : Описание операции}';
    
    protected $description = 'Списание суммы с баланса пользователя (защита от отрицательного баланса)';

    public function handle()
    {
        $amount = (float)$this->argument('amount');
        if ($amount <= 0) {
            $this->error('Сумма должна быть положительной');
            return 1;
        }

        try {
            $user = User::where('email', $this->argument('email'))->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->error("Пользователь с email '{$this->argument('email')}' не найден");
            return 3;
        }

        $balance = $user->balance()->firstOrCreate(['user_id' => $user->id], ['amount' => 0]);
        
        if ($balance->amount < $amount) {
            $this->error("Недостаточно средств. Текущий баланс: {$balance->amount}, пытались списать: {$amount}");
            return 2;
        }
        
        DB::transaction(function () use ($user, $balance, $amount) {
            $balance->decrement('amount', $amount);
            
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'debit',
                'description' => $this->option('description') ?? 'Списание средств',
            ]);
        });

        $this->info("Списано {$amount} с баланса пользователя {$user->name}. Новый баланс: {$balance->fresh()->amount}");
        return 0;
    }
}