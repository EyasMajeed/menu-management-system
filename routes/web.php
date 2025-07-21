<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ModifierGroupController;
use App\Http\Controllers\ModifierController;
use App\Http\Controllers\BranchWorkingHourController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});
// --- TEMPORARY TEST ROUTE FOR HTTP::FAKE() DEMONSTRATION ---
Route::get('/test-fake-http', function () {
    // This line tells Laravel: "If any Http client call tries to access
    // 'https://example.com/api/*' (or anything starting with it),
    // immediately return a 200 OK response with a JSON body of ['message' => 'Success!']"
    Http::fake([
        'https://example.com/api/*' => Http::response(['message' => 'Success!'], 200),
    ]);

    // This line makes an HTTP GET request to 'https://example.com/api/test'.
    // BUT, because Http::fake() is active, this request will NOT go out to the real internet.
    // It will be intercepted, and the fake response you defined above will be returned instantly.
    $response = Http::get('https://example.com/api/test');

    // This will dump the JSON content of the $response.
    // You will see: ['message' => 'Success!']
    dd($response->json());
});
// ------------------------------------------- MENU MANAGEMENT ---------------------------------------------

// Resource routes for Menus.
// This single line defines all standard CRUD routes: index, create, store, show, edit, update, destroy.
Route::resource('menus', MenuController::class);

// ------------------------------------------- NESTED RESOURCES UNDER MENUS ---------------------------------------------

// Categories nested under Menus
// This defines routes like:
// GET    /menus/{menu}/categories             -> menus.categories.index
// GET    /menus/{menu}/categories/create      -> menus.categories.create
// POST   /menus/{menu}/categories             -> menus.categories.store
// GET    /menus/{menu}/categories/{category}/edit -> menus.categories.edit
// PUT    /menus/{menu}/categories/{category}  -> menus.categories.update
// DELETE /menus/{menu}/categories/{category}  -> menus.categories.destroy
Route::resource('menus.categories', CategoryController::class);

// Items nested under Menus
// This defines routes like:
// GET    /menus/{menu}/items             -> menus.items.index
// GET    /menus/{menu}/items/create      -> menus.items.create
// POST   /menus/{menu}/items             -> menus.items.store
// GET    /menus/{menu}/items/{item}/edit -> menus.items.edit
// PUT    /menus/{menu}/items/{item}      -> menus.items.update
// DELETE /menus/{menu}/items/{item}      -> menus.items.destroy
Route::resource('menus.items', ItemController::class);

// Modifier Groups nested under Menus
Route::resource('menus.modifier-groups', ModifierGroupController::class);

// Modifiers nested under Menus
Route::resource('menus.modifiers', ModifierController::class);

// Branch Working Hours nested under Menus and Branches
// This is a deeper nested resource for specific working hours of a branch within a menu context.
// GET    /menus/{menu}/working-hours                               -> menus.working-hours.index (for all branches of that menu)
// GET    /menus/{menu}/branches/{branch}/working-hours/edit        -> menus.branches.working-hours.edit
// PUT    /menus/{menu}/branches/{branch}/working-hours             -> menus.branches.working-hours.update
Route::get('/menus/{menu}/working-hours', [BranchWorkingHourController::class, 'index'])->name('menus.working-hours.index');
Route::get('/menus/{menu}/branches/{branch}/working-hours/edit', [BranchWorkingHourController::class, 'edit'])->name('menus.branches.working-hours.edit');
Route::put('/menus/{menu}/branches/{branch}/working-hours', [BranchWorkingHourController::class, 'update'])->name('menus.branches.working-hours.update');

// This route will handle the POST request from the "Sync Menu" button
Route::post('menus/{menu}/sync', [MenuController::class, 'syncMenuToDeliveryApp'])->name('menus.sync');


// ------------------------------------------- ORDERS MANAGEMENT ---------------------------------------------

// Resource routes for Orders.
// This single line defines all standard CRUD routes for orders.
// It replaces all the individual 'orders' routes you had.
Route::resource('orders', OrderController::class);

// Custom routes for Order backups/restores (not part of standard CRUD)
Route::get('/backup-orders', [OrderController::class, 'backupOrders'])->name('orders.backup');
Route::get('/restore-orders', [OrderController::class, 'restoreOrders'])->name('orders.restore');

// ------------------------------------------- DEVELOPMENT NOTES (Remove in Production) ---------------------------------------------
// The following comments are development notes and are not part of the actual routing logic.
// They can be removed or kept for reference.

//add a new page steps:
//1-define a route in web.php:
//2-create or connect to a proper controller (by adding a public func for it):
//3-define a new view for it
//4-delete static html data

