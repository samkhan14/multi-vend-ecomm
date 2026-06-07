@extends('adminlayout.layout')

@section('content')
    @livewire('admin.user.user-edit', ['id' => $id])
@endsection
