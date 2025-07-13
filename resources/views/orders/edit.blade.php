@extends('layouts.app')

@section('content')
@section('title') Edit @endsection



<form method="POST" , action={{ route('orders.update', 1) }}>
    @csrf
    @method('PUT')
    <!-- Application Name -->
    <div class="mt-4">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card p-1 shadow w-100 mx-4" style="max-width: 800px;">
                <div class="card-body text-start">
                    <h5 class="card-title">Application Name:</h5>
                    <div class="input-group">
                        <select class="form-select" name="application" aria-label="Select application">
                            <option selected disabled>Choose Application...</option>
                            <option value="Jahez">Jahez</option>
                            <option value="ToYou">ToYou</option>
                            <option value="Hungerstation">Hungerstation</option>
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
                            <option value="Albaik">Albaik</option>
                            <option value="Altazaj">Altazaj</option>
                            <option value="California Burger">California Burger</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status -->
    <div class="mt-4">
        <div class="d-flex justify-content-center align-items-center">
            <div class="card p-1 shadow w-100 mx-4" style="max-width: 800px;">
                <div class="card-body text-start">
                    <h5 class="card-title">Order Status</h5>
                    <div class="input-group">
                        <select class="form-select" name="restaurant" aria-label="Select restaurant">
                            <option selected disabled>Choose Status...</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Altazaj">Rejected</option>
                            <option value="California Burger">Preparing</option>
                            <option value="California Burger">Ready To Pick Up</option>
                        </select>
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
        <button class="btn btn-primary" type="submit">Update</button>
    </div>

</form>







@endsection