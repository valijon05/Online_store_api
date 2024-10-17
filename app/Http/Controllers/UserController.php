<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Barcha foydalanuvchilarni ko'rsatish.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(User::all());
    }

    /**
     * Yangi foydalanuvchi yaratish.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Foydalanuvchini ko'rsatish.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Foydalanuvchini yangilash.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'phone' => 'nullable|string',
        ]);

        $user->update($request->only(['name', 'email', 'password', 'phone']));

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        return response()->json($user);
    }

    /**
     * Foydalanuvchini o'chirish.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([], 204);
    }
}
