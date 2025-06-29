<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Info(
 *     title="User Balance API",
 *     version="1.0.0",
 *     description="API для управления балансом пользователей"
 * )
 * 
 * @OA\Tag(
 *     name="Balance",
 *     description="Операции с балансом"
 * )
 */
class BalanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/balance/{email}",
     *     operationId="getUserBalance",
     *     tags={"Balance"},
     *     summary="Получить баланс пользователя",
     *     description="Возвращает текущий баланс пользователя по email",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="Email пользователя",
     *         @OA\Schema(type="string", format="email", example="ivan@example.com")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             @OA\Property(property="balance", type="number", format="float", example=1000.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function check($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        return response()->json([
            'balance' => $user->balance->amount ?? 0
        ]);
    }


    public function add(Request $request, $email)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'sometimes|string'
        ]);

        $user = User::where('email', $email)->firstOrFail();
        
        DB::transaction(function () use ($user, $request) {
            $user->balance()->updateOrCreate(
                ['user_id' => $user->id],
                ['amount' => DB::raw("amount + {$request->amount}")]
            );
            
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'type' => 'credit',
                'description' => $request->description ?? 'Пополнение баланса'
            ]);
        });

        return response()->json([
            'message' => 'Баланс успешно пополнен',
            'new_balance' => $user->balance->amount
        ]);
    }

    public function subtract(Request $request, $email)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'sometimes|string'
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $balance = $user->balance()->firstOrCreate(['user_id' => $user->id], ['amount' => 0]);

        if ($balance->amount < $request->amount) {
            return response()->json([
                'error' => 'Недостаточно средств на балансе'
            ], 400);
        }

        DB::transaction(function () use ($user, $request, $balance) {
            $balance->decrement('amount', $request->amount);
            
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'type' => 'debit',
                'description' => $request->description ?? 'Списание средств'
            ]);
        });

        return response()->json([
            'message' => 'Списание выполнено успешно',
            'new_balance' => $balance->fresh()->amount
        ]);
    }
}