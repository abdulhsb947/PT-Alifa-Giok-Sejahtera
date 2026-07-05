@extends('layouts.customer')

@section('content')

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">Orders</h1>

@foreach($orders as $o)
<div class="bg-white p-4 mb-4 rounded shadow">

    <h2 class="font-bold">{{ $o->order_code }}</h2>
    <p>{{ $o->project_name }}</p>
    <p>Status: {{ $o->status }}</p>

</div>
@endforeach

</div>

@endsection