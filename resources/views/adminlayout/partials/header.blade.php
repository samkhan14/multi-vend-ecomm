<header class="main-header bg-body position-relative d-none d-xl-block px-10 py-6">
    <div class="container-fluid">
        <nav class="navbar navbar-light py-0 row no-gutters px-3 px-lg-0">
            <div class="col-md-4 px-0 px-md-6 order-1 order-md-0">
                <form>
                    <div class="input-group position-relative bg-input rounded">
                        <input type="text" class="form-control border-1 pl-4 shadow-none" placeholder="Search Item">
                        <div class="input-group-append fs-14">
                            <button
                                class="btn btn-hover-bg-primary btn-hover-border-primary rounded-0 rounded-end border-start border-0 h-100 px-8 py-5">
                                <i class="far fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 d-flex flex-wrap justify-content-md-end align-items-center order-0 order-md-1">

             
                <div class="dropdown pl-2 py-2">
                    <a href="#"
                        class="dropdown-toggle text-heading pr-3 pr-sm-6 d-flex align-items-center dashboard-theme-primary-text justify-content-end"
                        data-bs-toggle="dropdown">
                        @if(Auth::user()->image)
                            <img src="{{ asset('storage/' . Auth::user()->image) }}"
                                alt="{{ Auth::user()->name }}"
                                id="header-profile-image"
                                class="rounded-circle"
                                style="width:40px; height:40px; object-fit:cover;">
                        @else
                            <!-- Default User Icon -->
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" 
                                id="header-profile-image"
                                style="width: 40px; height: 40px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif

                    </a>
                    <div class="dropdown-menu dashboard-theme-primary  dropdown-menu-end w-100" >
                        <a class="dropdown-item " href="{{ route('admin.profile-index') }}">My Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item " type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {  
            Livewire.on('profile-image-updated', (event) => {
                const headerImg = document.getElementById('header-profile-image');
                if (headerImg) {
                    headerImg.src = `/storage/${event.image}?t=${Date.now()}`;
                }
            });
        });
    </script>
@endpush
