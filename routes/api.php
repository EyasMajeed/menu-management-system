<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// This endpoint will receive the JSON payload from your MenuSyncService
// and log it, simulating a delivery platform API.
Route::post('/mock-menu-sync', function (Request $request) {
    // Log the entire incoming JSON payload
    Log::info('Mock Menu Sync Received Payload:', $request->json()->all());

    // You can also log specific parts if needed
    // Log::info('Received event:', ['event' => $request->input('event')]);
    // Log::info('Received branch_id:', ['branch_id' => $request->input('branch_id')]);

    // Return a success response, as a real API would
    return response()->json([
        'status' => 'success',
        'message' => 'Menu sync received by mock API successfully!',
        'received_data_keys' => array_keys($request->json()->all()) // Just for confirmation
    ], 200);
});
