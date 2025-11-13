<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BalanceHistory;

class BalanceController extends Controller
{
    // Increment balance for a user
    public function incrementBalance(Request $request, $id)
    {
        // 1. Validate input
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        // 2. Find user
        $user = User::findOrFail($id);

        // 3. Increment balance
        $user->balance += $request->amount;
        $user->save();

        BalanceHistory::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'balance_after' => $user->balance,
            'type' => 'credit',
            'note' => $request->note ?? 'Balance Credited',
        ]);

        // 4. Return response
        return response()->json([
            'message' => 'Balance incremented successfully',
            'user' => $user
        ]);
    }


    public function decrementBalance(Request $request, $id)
{
    // 1. Validate input
    $request->validate([
        'amount' => 'required|numeric|min:0.01',
    ]);

    // 2. Find user
    $user = User::findOrFail($id);

    // 3. Check for sufficient balance
    if ($user->balance < $request->amount) {
        return response()->json([
            'message' => 'Insufficient balance'
        ], 400);
    }

    // 4. Decrement balance inside transaction
        $user->balance -= $request->amount;
        $user->save();

        BalanceHistory::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'balance_after' => $user->balance,
            'type' => 'debit',
            'note' => $request->note ?? 'Balance Debited',
        ]);
    

    // 5. Return response
    return response()->json([
        'message' => 'Balance debited successfully',
        'user' => $user
    ]);
}

    
}
