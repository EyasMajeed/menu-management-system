@extends('layouts.app')

@section('content')
@section('title') show @endsection

<div class="card mt-4">
    <div class="card-header">
        Order Info
    </div>
    <div class="card-body">
        <h5 class="card-title">Application: {{$app->name}}</h5>
        <p class="card-text">Resturant: {{$resturant->name}}</p>
        <p class="card-text">ID: {{$order->id}}</p>
    </div>
</div>



<div class="card mt-4">
    <div class="card-header">
        Client Info
    </div>
    <div class="card-body">
        <h5 class="card-title">Name: {{$user->name}}</h5>
        <p class="card-text">ID: {{$order->id}}</p>
        <p class="card-text">Order Details: {{$order->details}}</p>
        <p class="card-text">Created At: {{$order->created_at}}</p>
    </div>
</div>

@endsection