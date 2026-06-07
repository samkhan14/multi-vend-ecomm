@extends('adminlayout.layout')

@section('content')
    @livewire('admin.vendor.vendor-detail', ['id' => $id])
@endsection