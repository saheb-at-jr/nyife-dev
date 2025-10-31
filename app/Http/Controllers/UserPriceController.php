<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserPriceController extends Controller
{
    public function updatePrices(Request $request, User $user)
    {
        $validated = $request->validate([
            'marketing_price' => 'required|numeric|min:0',
            'auth_price'      => 'required|numeric|min:0',
            'utility_price'   => 'required|numeric|min:0',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Prices updated successfully ✅',
            'user'    => $user,
        ]);
    }

    public function getPrices(User $user)
    {
        return response()->json([
            'marketing_price' => $user->marketing_price,
            'auth_price'      => $user->auth_price,
            'utility_price'   => $user->utility_price,
        ]);
    }
}
