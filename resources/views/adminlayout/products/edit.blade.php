    @extends('adminlayout.layout')

    @section('content')
        @livewire('admin.products.products-edit', [
            'id' => $id,
            'slug' => $slug
        ])
    @endsection