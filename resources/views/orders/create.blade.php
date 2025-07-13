@extends('layouts.app')

@section('content')
@section('title') Create @endsection

{{-- to create an order you need to enter the following:
1- application name: done
2- resturant name --}}

<form method="POST" action="{{ route('orders.store') }}">
    @csrf


    <!-- Client Name -->
    <div class="mt-4">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card p-1 shadow w-100 mx-4" style="max-width: 800px;">
                <div class="card-body text-start">
                    <h5 class="card-title">Client Name:</h5>
                    <div class="input-group">
                        <select class="form-select" name="name" aria-label="Select application">
                            <option selected disabled>Choose Client...</option>
                            @foreach ($users as $user)
                               <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Name -->
    <div class="mt-4">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card p-1 shadow w-100 mx-4" style="max-width: 800px;">
                <div class="card-body text-start">
                    <h5 class="card-title">Application Name:</h5>
                    <div class="input-group">
                        <select class="form-select" name="application" aria-label="Select application">
                            <option selected disabled>Choose Application...</option>
                            @foreach ($applications as $application)
                                <option value="{{ $application->id }}"> {{ $application->name }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurant Name -->
    <div class="mt-4">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card p-1 shadow w-100 mx-4" style="max-width: 800px;">
                <div class="card-body text-start">
                    <h5 class="card-title">Restaurant Name:</h5>
                    <div class="input-group">
                        <select class="form-select" name="restaurant" aria-label="Select restaurant">
                            <option selected disabled>Choose Restaurant...</option>
                            @foreach ($resturants as $resturant)
                                <option value=" {{ $resturant->id }} "> {{ $resturant->name }} </option>
                            @endforeach                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="mt-4">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card p-1 shadow w-100 mx-4" style="max-width: 800px;">
                <div class="card-body text-start">
                    <h5 class="card-title">Order Details:</h5>
                    <div class="mb-3 mx-4">
                        <textarea class="form-control w-100" name="details" rows="5"
                            placeholder="Enter order details here..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Submit Button -->
    <div class="mt-4 mb-5 text-center">
        <button class="btn btn-success" type="submit">Submit</button>
    </div>

</form>







@endsection