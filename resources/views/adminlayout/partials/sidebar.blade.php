<div class="db-sidebar bg-body">
    <aside class="navbar navbar-expand-xl navbar-light d-block px-0 header-sticky dashboard-nav py-0">
        <div class="sticky-area border-right">
            <div class="d-flex px-6 px-xl-10 w-100 border-bottom py-7 justify-content-between">
                <a href="{{ route('admin.dashboard') }}" class="navbar-brand py-4">
                    @if (siteSetting() && siteSetting()->admin_logo)
                        <img id="sidebar-admin-logo" src="{{ asset('storage/' . siteSetting()->admin_logo) }}"
                            alt="Admin Logo" style="height:60px; max-width:180px; object-fit:contain;">
                    @else
                        <img id="sidebar-admin-logo" src="{{ asset('assets/images/others/logo.png') }}"
                            alt="Default Logo" style="height:60px; max-width:180px; object-fit:contain;">
                    @endif
                </a>



                <div class="ml-auto d-flex align-items-center ">
               
                    <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse"
                        data-bs-target="#primaryMenuSidebar" aria-controls="primaryMenuSidebar" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
            <div class="collapse navbar-collapse bg-body position-relative z-index-5" id="primaryMenuSidebar">
                <form class="d-block d-xl-none pt-8 px-6">
                    <div class="input-group position-relative bg-body-tertiary">
                        <input type="text" class="form-control border-0 bg-transparent pl-4 shadow-none"
                            placeholder="Search Item">
                        <div class="input-group-append fs-14px px-6 border-start border-2x ">
                            <button class="bg-transparent border-0 outline-none py-5">
                                <i class="far fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <ul class="list-group list-group-flush list-group-no-border w-100 p-6">
                    @can('dashboard.view')
                    <li
                        class="list-group-item px-0 py-0 sidebar-item mb-3 border-0 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-heading text-decoration-none lh-1 sidebar-link py-5 px-6 d-flex align-items-center"
                            title="Dashboard">
                            <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                                <i class="fas fa-home-lg-alt"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Dashboard</span>
                        </a>
                    </li>
                    @endcan
                    @can('banners.view')
                    <li
                        class="list-group-item px-0 py-0 sidebar-item mb-3 border-0 {{ request()->routeIs('admin.banner') ? 'active' : '' }}">
                        <a href="{{ route('admin.banner') }}"
                            class="text-heading text-decoration-none lh-1 sidebar-link py-5 px-6 d-flex align-items-center"
                            title="Banner">
                            <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                                <i class="fas fa-ad"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Banners</span>
                        </a>
                    </li>
                    @endcan

                    @canany(['products.view', 'categories.view', 'brands.view', 'attributes.view', 'variants.view'])
                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#product"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse"
                            aria-expanded="{{ request()->routeIs('admin.brand*', 'admin.categories*') ? 'true' : 'false' }}"
                            title="Products">
                            <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                <i class="fas fa-shopping-bag"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Catalog Management</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse {{ request()->routeIs('admin.brand*', 'admin.categories') ? 'show' : '' }}"
                            id="product">
                            <ul class="sub-menu list-unstyled">
                                @can('products.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.product') }}" title="Product List">Products</a>
                                </li>
                                @endcan
                                @can('categories.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.categories') }}" title="Product Grid">Categories</a>
                                </li>
                                @endcan
                                @can('brands.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold {{ request()->routeIs('admin.brand*') ? 'active' : '' }}"
                                        href="{{ route('admin.brand') }}" title="Product Grid 2">Brands
                                    </a>
                                </li>
                                @endcan
                                @can('attributes.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.attribute') }}" title="Categoried">Attributes</a>
                                </li>
                                @endcan
                                @can('variants.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.variant') }}" title="Categoried">Variants</a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcan


                    @can('coupons.view')
                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#marketing"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse" aria-expanded="false" title="marketing">
                            <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                <i class="fa-solid fa-bullhorn"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Marketing</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse" id="marketing">
                            <ul class="sub-menu list-unstyled">
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.coupon') }}" title="Order List 1">Coupans &
                                        Discount</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endcan


                    @canany(['users.view', 'subscribers.view', 'inquiries.view'])
                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#sellers"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse" aria-expanded="false" title="Sellers">
                            <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                <i class="fas fa-users"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Manage Users</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse" id="sellers">
                            <ul class="sub-menu list-unstyled">
                                @canany(['users.view', 'user.create', 'user.edit', 'user.detail'])
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.user') }}" title="Sellers Cards">Users List</a>
                                </li>
                                @endcanany
                                {{-- @can('vendors.view') --}}
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.vendor') }}" title="Sellers List">Vendors List</a>
                                </li>
                                {{-- @endcan --}}
                                @can('subscribers.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.user.subscriber') }}" title="Sellers List">Newsletter
                                        Subscribers</a>
                                </li>
                                @endcan
                                
                                @can('inquiries.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.inquiries') }}" title="Sellers List">Inquiries</a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcan


                    @canany(['ratings.view', 'ratings.detail'])
                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#add_product"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse" aria-expanded="false" title="Add Product">
                            <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                                <i class="fas fa-comment-alt"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Product Review</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse" id="add_product">
                            <ul class="sub-menu list-unstyled">
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.rating') }}" title="Add Product 1">Reviews</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endcanany
                    @canany(['orders.view', 'orders.detail'])
                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#order"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse" aria-expanded="false" title="Order">
                            <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Order</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse" id="order">
                            <ul class="sub-menu list-unstyled">
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.orders') }}" title="Order List 1">Orders Listing</a>
                                </li>
                        </div>
                    </li>
                    @endcanany

                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#transaction"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse" aria-expanded="false" title="finance">
                            <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                <i class="fas fa-circle-dollar-to-slot"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Finance</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse" id="transaction">
                            <ul class="sub-menu list-unstyled">
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ Route('admin.e-wallet') }}" title="Transactions 1">E-Wallet </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ Route('admin.payouts') }}" title="Transactions 2">Payouts</a>
                                </li>
                            </ul>
                        </div>
                    </li>


                  @canany(['announcements.view', 'pages.view', 'about.view'])
                        <li
                            class="list-group-item px-0 py-0 dashboard-theme-primary sidebar-item mb-3 has-children border-0">
                            <a href="#cms-panel"
                                class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                                data-bs-toggle="collapse" aria-expanded="false" title="cms-panel">
                                <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                    <i class="fas fa-file-alt dashboard-theme-primary-text"></i>
                                </span>
                                <span class="sidebar-item-text fs-14px fw-semibold dashboard-theme-primary-text">Cms Pages
                                    Info</span>
                                <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                        class="far fa-angle-down dashboard-theme-primary-text"></i></span>
                            </a>
                            <div class="collapse menu-collapse" id="cms-panel">
                                <ul class="sub-menu list-unstyled">
                                    @canany(['announcements.view', 'announcements.create', 'announcements.edit', 'announcements.delete'])
                                        <li class="sidebar-item">
                                            <a class="sidebar-link dashboard-theme-primary-text pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                                href="{{ route('admin.annoucement') }}" title="User login">Annoucements</a>
                                        </li>
                                    @endcanany
                                    @canany(['pages.view', 'pages.create', 'pages.edit', 'pages.delete'])
                                        <li class="sidebar-item">
                                            <a class="sidebar-link pe-5 ps-8 dashboard-theme-primary-text py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                                href="{{ route('admin.page-content') }}" title="User registration">Policy Pages
                                            </a>
                                        </li>
                                    @endcanany
                                    @can('about.view')
                                        <li class="sidebar-item">
                                            <a class="sidebar-link pe-5 ps-8 dashboard-theme-primary-text py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                                href="{{ route('admin.about') }}" title="User registration">About Page
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcanany

                    @canany(['roles.view', 'permissions.view'])
                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#permission"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse" aria-expanded="false" title="permission">
                            <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                <i class="far fa-user-shield"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Roles And Permissions</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse" id="permission">
                            <ul class="sub-menu list-unstyled">
                                @canany(['roles.view', 'roles.create', 'roles.edit', 'roles.delete'])
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.role-index') }}" title="User login">Manage Roles</a>
                                </li>
                                @endcanany
                                @canany(['permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'])
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.permission-index') }}" title="User login">Manage Permissions</a>    
                                </li>
                                @endcanany
                            </ul>
                        </div>
                    </li>
                    @endcanany
                           @can('social_links.view')
                        <li class="list-group-item px-0 py-0 dashboard-theme-primary sidebar-item mb-3 border-0">
                            <a href="{{ route('admin.social-links.index') }}" 
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative" 
                            title="Social Media Links">
                                <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                    <i class="fas fa-share-alt dashboard-theme-primary-text"></i>
                                </span>
                                <span class="sidebar-item-text fs-14px fw-semibold dashboard-theme-primary-text">
                                    Social Links
                                </span>
                            </a>
                        </li>
                    @endcan
                                       @can('integrations.view')
                    <li
                        class="list-group-item px-0 py-0 sidebar-item mb-3 border-0">
                        <a href="{{ route('admin.integrations') }}"
                            class="text-heading text-decoration-none lh-1 sidebar-link py-5 px-6 d-flex align-items-center"
                            title="Banner">
                            <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                                <i class="fas fa-plug"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Integrations</span>
                        </a>
                    </li>
                    @endcan


                    @canany(['seo.view', 'settings.view', 'shipping.view', 'subscribers.view', 'profile.view', 'payment_gateways.view', 'general.view', 'site.view'])
                    <li class="list-group-item px-0 py-0 sidebar-item mb-3 has-children border-0">
                        <a href="#seo-setting"
                            class="text-heading text-decoration-none lh-1 d-flex sidebar-link align-items-center py-5 px-6 position-relative"
                            data-bs-toggle="collapse" aria-expanded="false" title="seo Setting">
                            <span class="sidebar-item-icon d-inline-block w-40px text-muted">
                                <i class="fas fa-cog"></i>
                            </span>
                            <span class="sidebar-item-text fs-14px fw-semibold">Setting</span>
                            <span class="position-absolute top-50 end-5 translate-middle-y"><i
                                    class="far fa-angle-down"></i></span>
                        </a>
                        <div class="collapse menu-collapse" id="seo-setting">
                            <ul class="sub-menu list-unstyled">
                                @canany(['seo.view', 'seo.create', 'seo.delete', 'seo.edit'])
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.seo') }}" title="Profile settings">Site Seo
                                        settings</a>
                                </li>
                                @endcanany
                                @canany(['site.view'])
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.site-setting') }}" title="Site settings">Site
                                        settings</a>
                                </li>
                                @endcan
                                @can('general.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.general-setting') }}" title="Site settings">General
                                        settings</a>
                                </li>
                                @endcan
                                @can('shipping.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.shipping-setting') }}" title="Site settings">Shipping
                                        settings</a>
                                </li>
                                @endcan
                                @can('payment_gateways.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold {{ request()->routeIs('admin.payment-gateways') ? 'active' : '' }}"
                                        href="{{ route('admin.payment-gateways') }}" title="Payment gateways">Payment
                                        Gateways</a>
                                </li>
                                @endcan
                                @can('profile.view')
                                <li class="sidebar-item">
                                    <a class="sidebar-link pe-5 ps-8 py-5 lh-1 text-decoration-none fs-14px fw-semibold"
                                        href="{{ route('admin.profile-index') }}" title="Site settings">Profile
                                        settings</a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                    @endcan

                </ul>
            </div>
        </div>
    </aside>


</div>
