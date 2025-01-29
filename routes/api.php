<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\StackOverflowController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/stackoverflow/questions', [StackOverflowController::class, 'getQuestions']);

Route::apiResource('products', ProductController::class);

