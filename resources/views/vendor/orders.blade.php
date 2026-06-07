@extends('adminlayout.layout')

@section('content')
    @livewire('admin.vendor.vendor-order-items', ['id' => $id])
@endsection
