@extends('layouts.app')

@section('content')
@section('title') index @endsection

<div class="mt-5">
  <ul class="nav justify-content-center">
    <div class="d-flex justify-content-around bg-white rounded-pill shadow p-3" style="width: max-content; gap: 1rem;">

      <button class="btn text-black rounded-pill d-flex align-items-center" style="background-color: #2DFFB4;">
        Active
        <span class="badge bg-white text-dark ms-2">8</span>
      </button>

      <button class="btn btn-white rounded-pill d-flex align-items-center">
        Scheduled
        <span class="badge rounded-pill ms-2" style="background-color: #d3fff1; color: #000000;">8</span>
      </button>

      <button class="btn btn-white rounded-pill d-flex align-items-center">
        Pick Up
        <span class="badge rounded-pill ms-2" style="background-color: #d3fff1; color:#000000">8</span>
      </button>

      <button class="btn btn-white rounded-pill d-flex align-items-center">
        Completed
        <span class="badge rounded-pill ms-2" style="background-color: #d3fff1; color:#000000">24</span>
      </button>

    </div>
  </ul>
</div>

{{-- //@dd means print what inside the orders and stop the execution
so waht comes after it, dont work
example:
@dd($orders) --}}

<div class="mt-5 ms-5 me-5">
  <div class="text-center">

    <div class="mt-3">
      <div class="card shadow">
        <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Application</th>
                <th scope="col">Resturant</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($orders as $order)
              <tr>
                <th scope="col">{{$order->id}}</th>
                <td>{{$order->app}}</td>
                <td>{{$order->resturant}}</td>
                <td>
                  <div>
                    <span class="badge px-3 py-2 rounded-pill text-black" style="
        background-color: {{
             $order['status']  === 'Accepted' ? '#2DFFB4' :
            ( $order['status']  === 'Preparing' ? '#FFA500' :
            ( $order['status']  === 'Ready To Pick Up' ? '#1E90FF' :
            ( $order['status'] === 'Completed' ? '#d3fff1' :'#6c757d')))
        }};
        font-weight: 450;
        font-size: 1rem;
        padding: 0.75rem 1.5rem
        border radius: 10px">
                      {{ ucfirst( $order['status'] ) }}
                    </span>
                  </div>
                </td>
                <td>

                  <a href="{{route('orders.show', $order['id'])}}" class="btn btn-info">View</a>
                  <a href="{{route('orders.edit', $order['id'])}}" class="btn btn-primary"> edit </a>
                  <form style="display: inline;" , method="POST" , action="{{ route('orders.destroy', $order['id']) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div>
        <div>
          <div>

            <div class="mt-4 mb-5 text-center">
              <a href="{{ route('orders.create') }}" class="btn text-black"
                style="background-color: #2effb6; text-decoration: none; border-radius: 10px;">
                Create Order
              </a>
            </div>



            @endsection()