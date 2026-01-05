<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsers(){
        $users = User::all();
        return response()->json(['users' => $users]);
    }

    public function getUser($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['user' => $user]);
    }

    public function deleteUser($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function updateUser(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,'.$id,
            'role' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255|unique:users,email,'.$id,
            'password' => 'sometimes|string|min:6',
        ]);

        $user->update($validated);
        return response()->json(['message' => 'User updated', 'user' => $user]);
    }
}
