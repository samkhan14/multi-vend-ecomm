@extends('layouts.vendor')

@section('vendor')
    <div class="container-fluid p-0">
        <livewire:vendor.reset-password :token="$token" />
    </div>
@endsection
