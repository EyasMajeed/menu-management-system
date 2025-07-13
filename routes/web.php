<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ModifierGroupController;
use App\Http\Controllers\ModifierController;
use App\Http\Controllers\BranchWorkingHourController;

Route::get('/', function () {
    return view('welcome');
});

//-------------------------------------------MENU---------------------------------------------
// for the menu:
Route::resource('menus', MenuController::class);

Route::get ('/menus',[MenuController::class, 'index'])->name('menus.index');

//create menu
Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');

//store menu to db
Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');

//show certain menu
Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');

// Edit menu form
Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');

// Update menu (PUT method for saving edited info)
Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');

// Delete menu
Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

//Tabs on the show menu
Route::get('/menus/{menu}/categories', [CategoryController::class, 'index'])->name('menus.categories.index');
Route::get('/menus/{menu}/items', [ItemController::class, 'index'])->name('menus.items.index');




//--------------------------Category and item--------------------

//create and store
Route::get('menus/{menu}/categories/create', [CategoryController::class, 'create'])->name('categories.create');
Route::post('menus/{menu}/categories', [CategoryController::class, 'store'])->name('categories.store');

Route::get('menus/{menu}/items/create', [ItemController::class, 'create'])->name('items.create');
Route::post('menus/{menu}/items', [ItemController::class, 'store'])->name('items.store');


// Categories
// Show Details
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Show edit form
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');

// Submit update
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');

// Delete category
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


// Items
// Show edit form
Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');

// Submit update
Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');

// Delete category
Route::delete('/menus/{menu}/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

//---------------------------------Modifiers and Modifiers Groups-------------------------------------
// Routes for Modifier Groups nested under Menu
Route::get('/menus/{menu}/modifier-groups', [ModifierGroupController::class, 'index'])->name('menus.modifier-groups.index');
Route::get('/menus/{menu}/modifier-groups/create', [ModifierGroupController::class, 'create'])->name('menus.modifier-groups.create');
Route::post('/menus/{menu}/modifier-groups', [ModifierGroupController::class, 'store'])->name('menus.modifier-groups.store');
Route::get('/menus/{menu}/modifier-groups/{modifierGroup}/edit', [ModifierGroupController::class, 'edit'])->name('menus.modifier-groups.edit');
Route::put('/menus/{menu}/modifier-groups/{modifierGroup}', [ModifierGroupController::class, 'update'])->name('menus.modifier-groups.update');
Route::delete('/menus/{menu}/modifier-groups/{modifierGroup}', [ModifierGroupController::class, 'destroy'])->name('menus.modifier-groups.destroy');



// define them as standalone, but linked via modifier_group_id in controller
Route::get('/menus/{menu}/modifiers', [ModifierController::class, 'index'])->name('menus.modifiers.index');
Route::get('/menus/{menu}/modifiers/create', [ModifierController::class, 'create'])->name('menus.modifiers.create');
Route::post('/menus/{menu}/modifiers', [ModifierController::class, 'store'])->name('menus.modifiers.store');
Route::get('/menus/{menu}/modifiers/{modifier}/edit', [ModifierController::class, 'edit'])->name('menus.modifiers.edit');
Route::put('/menus/{menu}/modifiers/{modifier}', [ModifierController::class, 'update'])->name('menus.modifiers.update');
Route::delete('/menus/{menu}/modifiers/{modifier}', [ModifierController::class, 'destroy'])->name('menus.modifiers.destroy');


// This route will display the working hours for all branches associated with a menu.
Route::get('/menus/{menu}/working-hours', [BranchWorkingHourController::class, 'index'])->name('menus.working-hours.index');

// Route to show the edit form for a specific branch's working hours
Route::get('/menus/{menu}/branches/{branch}/working-hours/edit', [BranchWorkingHourController::class, 'edit'])->name('menus.branches.working-hours.edit');

// Route to update a specific branch's working hours
Route::put('/menus/{menu}/branches/{branch}/working-hours', [BranchWorkingHourController::class, 'update'])->name('menus.branches.working-hours.update');

//-------------------------------------ORDERS------------------------------------------------
//show all orders route
Route::get('/orders', [OrderController::class, 'index'])-> name('orders.index');

//create orders route
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');

//stroe route
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');


//show orders info route:
//route priciple naming: URI -> /photos/{photo}, Action: show, RouteName: orders.show
//anything between the {} they are dynamic and can be anything
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');


//edit order
Route::get('/orders/{order}/edit',[OrderController::class, 'edit'])->name('orders.edit');


//update order
Route::put('/oders/{order}', [OrderController::class, 'update'])->name('orders.update');

//destroy order
Route::delete('/orders/{order}',[OrderController::class, 'destroy'])->name('orders.destroy');


//back up data
Route::get('/backup-orders', [OrderController::class, 'backupOrders']);

//restore data
Route::get('/restore-orders', [OrderController::class, 'restoreOrders']);

//struct change: for db(create table, edit col, , remove col)
//oper on db (insert or edit or delte a rec)

//for each table in db you have a model for it that is (singular, eg..orders in db the model name is Order.php)



//add a new page steps:
//1-define a route in web.php:

//2-create or connect to a proper controller (by adding a public func for it): done

//3-define a new view for it

//4-delete static html data








//1- define a route for the user to access through browser: done.

//2- define a controller for the view: done. 

//3- define a view that contains list of orders: done
    
//4- remove any static html data from the view

