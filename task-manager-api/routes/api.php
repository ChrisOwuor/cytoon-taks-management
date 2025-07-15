<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Public auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [Authcontroller::class, 'login']);
});


// Admin-only routes
Route::group(['prefix' => 'tasks', 'middleware' => ['auth:api', 'role:admin']], function () {
    Route::get('/', [TaskController::class, 'index']);   // Admin can view all tasks
    Route::post('/add', [TaskController::class, 'store']);  // Admin can create/assign tasks
    Route::put('/{id}', [TaskController::class, 'update']); // Update task status

});

// User routes
Route::group(['prefix' => 'tasks', 'middleware' => ['auth:api', 'role:user']], function () {
    Route::get('/user-tasks', [TaskController::class, 'userTasks']); // User can view their assigned tasks
    Route::get('/{id}', [TaskController::class, 'show']); // User
    Route::put('/status-update/{id}', [TaskController::class, 'updateTaskStatus']); // User can update task status
});

// User routes for user management
Route::group(['prefix' => 'users', 'middleware' => ['auth:api', 'role:admin']], function () {
    Route::get('/', [UserController::class, 'index']); // Retrieve all users
    Route::get('/{id}', [UserController::class, 'show']); // Retrieve a specific user by ID
    Route::put('/{id}', [UserController::class, 'update']); // Update user data
    Route::delete('/{id}', [UserController::class, 'destroy']); // Delete a user by ID
    Route::post('/create', [UserController::class, 'create']); // Create a new user
});
Route::group( ['middleware' => ['auth:api', 'role:admin,user']],function () {
        Route::get('/user',  [UserController::class, 'me']);
        Route::get('/stats', [TaskController::class, 'stats']);
    }
);

Route::fallback(function () {
    return response()->json(['message' => 'Not Found'], 404);
});
