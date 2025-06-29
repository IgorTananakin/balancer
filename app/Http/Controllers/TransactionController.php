<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class TransactionController extends Controller
{
    /**
     * Получение транзакций пользователя по email
     *
     * @param string $email
     * @param Request $request
     * @return JsonResponse
     */
    public function index(string $email, Request $request): JsonResponse
    {
        $limit = $request->query('limit');
        
        $user = User::with(['transactions' => function($query) use ($limit) {
            $query->latest();
            if ($limit) {
                $query->take((int)$limit);
            }
        }])->where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $transactions = $user->transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
                'description' => $transaction->description,
                'created_at' => $transaction->created_at->toDateTimeString()
            ];
        });

        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
