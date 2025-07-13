<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Application;
use App\Models\Resturant;

class OrderController extends Controller
// the view should have the same name as the function(index)
{
    public function index(Request $request)
    { 
        //retrive data from db: select* from orders
        $ordersFromDB=Order::all();  //collection of objects
        if ($request->wantsJson()) {
            return response()->json ([
                'status' => 'success',
                'data' => $ordersFromDB
            ]);
        }

        //id, app(var char), resturant (var char), 
        return view ('orders.index', ['orders'=>$ordersFromDB]);
    }

    // convention over config
    public function show (Order $order) //Type hinting (route model binding) coding less
    {
        //$singleOrderFromDB = Order::findOrfail($orderId);//model object retrive one object only
        //$singleOrderFromDB = Order::where('id', $orderId);//eloquent builder --> (wait for more query)
        //$singleOrderFromDB = Order::where('id', $orderId)->first();//model object --> select* from where orderId limit 1
        //$singleOrderFromDB = Order::where('id', $orderId)-get();//collcetion of object --> select* from where orderId


        //task: represnt all the info of the order with its related id
        //the order id is in $order
        //get all the required data with their connected id:
        $applicationMatched=Application::where('id', $order->id)->first();
        $resturantMatched=Resturant::where('id', $order->id)->first();
        $useInfoMatched=User::where('id', $order->id)->first();

        return view('orders.show', [
        'order'     => $order,
        'app'       => $applicationMatched,
        'resturant' => $resturantMatched,
        'user'      => $useInfoMatched,
         ]);
    }

    public function create ()
    {
        //for representing options variables
        $users = User::all();
        $resturants = Resturant::all();
        $applications = Application::all();


        return view ('orders.create', 
        ['users' => $users,
         'resturants' => $resturants,
         'applications' => $applications] );
    }

    public function edit()
    {
        
        return view ('orders.edit');
    }

    public function store ()
    {
        //1-get user data
        $data = request()->all();

        $clientName = request()->name;
        $applicationName = request()->application;
        $resturantName = request()->resturant;
        $orderDetails = request()->details;
         


        //2-store the submitted data in database
        Order::create([
            'details'=>'$orderDetails',
        ]);
        

        //redirection to orders.index
        return to_route('orders.index');
    }

    public function update ()
    {
        //1-get user data
        $title = request()->title;
        $descriptioin = request()->description;
        $postCreator = request()->post_creator;
        //2-edit them in db

        //3-redirect to orders.show
        return to_route('orders.show',1);
    }

    public function destroy()
    {
        //1-delete the order from DB

        //2-redirect to orders.index
        return to_route('orders.index');
    }

    public function backupOrders()
    {
        $orders = DB::table('orders')->get();//backup the table
        Storage::put('orders_backup.json', $orders->toJson());  //store as file in storate/app
        return 'Orders backed up!';
    }
 
}
