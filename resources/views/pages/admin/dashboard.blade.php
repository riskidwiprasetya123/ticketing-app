@extends('layouts.admin_layouts')

@section('title', 'Dashboard')

@section('content')

<div class="bg-white rounded-xl shadow p-8">

    <h1 class="text-3xl font-bold">
        Halo, {{ auth()->user()->name }} 👋
    </h1>

    <p class="mt-3 text-gray-600">
        Selamat datang di Dashboard Admin BengTix.
    </p>

</div>

@endsection