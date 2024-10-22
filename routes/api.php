<?php

use App\Http\Controllers\StackOverflowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/stackoverflow/questions', [StackOverflowController::class, 'getQuestions']);
