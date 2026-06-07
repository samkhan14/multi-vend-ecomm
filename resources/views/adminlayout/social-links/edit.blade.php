@extends('adminlayout.layout')
@section('content')
    @livewire('admin.social-links.social-links-edit', ['id' => $id])
@endsection