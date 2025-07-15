<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function me(Request $request)
    {
        // retrieve currently logged in user data
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'user' => $user,
        ], Response::HTTP_OK);
    }
    public function index(Request $request)
    {
        // retrieve all users
        $page = $request->query('page', 1);

        $users = User::paginate(10, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Users retrieved successfully',
            'users' => $users,
        ], Response::HTTP_OK);
    }
    public function show($id)
    {
        // get a user by the user id
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'user' => $user,
        ], Response::HTTP_OK);
    }
    public function update(Request $request, $id)
    {
        // Find the user
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ], Response::HTTP_OK);
    }
    public function destroy($id)
    {
        // delete a user
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], Response::HTTP_NO_CONTENT);
    }
    public function create(CreateUserRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'role' => $validatedData['role'],
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());

            return response()->json([
                'message' => "Failed to create user " . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
