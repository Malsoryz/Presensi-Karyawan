<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function checkApproval(Request $request)
    {
        $id = $request->query('id');
        $user = User::find($id);
        $isApproved = (bool) $user->status_approved;
        return response()->json([
            'is_approved' => $isApproved,
        ]);
    }
}
