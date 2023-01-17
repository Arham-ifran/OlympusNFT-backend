@php
$segment2 = Request::segment(2);
$segment3 = Request::segment(3);
$user = Auth::guard('admin')->user();
@endphp
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/admin') }}" class="brand-link">
        @if(SITE_LOGO <> '' && SITE_LOGO <> null)
        <img src="{!! checkImage(asset('storage/uploads/images/'.@SITE_LOGO)) !!}" alt="" class="brand-image img-circle elevation-3" style="opacity: .8">
        @endif
        <span class="brand-text font-weight-light">{{SITE_NAME}}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{!! checkImage(asset('storage/uploads/admins/'.Auth::user()->id.'/'.Auth::user()->photo)) !!}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ url('admin/profile') }}" class="d-block">{!! Auth::user()->firstname . ' ' . Auth::user()->lastname!!}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                <li class="nav-item">
                    <a href="{{ url('/admin/dashboard') }}" class="nav-link @if ($segment2 == 'dashboard') active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>


                @if ($user->can('Edit Setting') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'site-settings' ) menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'site-settings' ) open active @endif">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Settings
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'site-settings') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/site-settings') }}" class="nav-link  @if ($segment2 == 'site-settings') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Site Settings</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif

                @if ($user->can('View Category') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'categories' ) menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'categories' ) open active @endif">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>
                            Categories
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'categories') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/categories') }}" class="nav-link  @if ($segment2 == 'categories') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Categories</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif

                @if ($user->can('View Role') || $user->can('View Permission') || $user->hasRole('Super Admin'))
                <li class="nav-item  @if ($segment2 == 'roles' || $segment2 == 'permissions') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'roles' || $segment2 == 'permissions') active @endif">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Roles & Permissions
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'roles' || $segment2 == 'permissions') block @endif">
                        @if ($user->can('View Role') || $user->hasRole('Super Admin'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/roles') }}" class="nav-link  @if ($segment2 == 'roles') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        @endif
                        @if ($user->can('View Permission') || $user->hasRole('Super Admin'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/permissions') }}" class="nav-link  @if ($segment2 == 'permissions') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if ($user->can('View Admin Users') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'users') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'users') active @endif">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                            Admin Users
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'users') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/users') }}" class="nav-link  @if ($segment2 == 'users') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Admin Users</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Store') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'stores') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'stores') active @endif">
                        <i class="nav-icon fas fa-store-alt"></i>
                        <p>
                            Stores
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'stores') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/stores') }}" class="nav-link  @if ($segment2 == 'stores') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stores </p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <!-- <li class="nav-item">
                    <a href="#" class="nav-link @if ($segment2 == 'all-users') active @endif">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>
                           All Users
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'all-users') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/all-users') }}" class="nav-link  @if ($segment2 == 'all-users') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>All Users </p>
                            </a>
                        </li>
                    </ul>
                </li> -->
                @if ($user->can('View Investor') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'investors') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'investors') active @endif">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Investor
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'investors') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/investors') }}" class="nav-link  @if ($segment2 == 'investors') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Investor</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Artists') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'artists') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'artists') active @endif">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Artists
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'artists') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/artists') }}" class="nav-link  @if ($segment2 == 'artists') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Artists</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Musicians') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'musicians') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'musicians') active @endif">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Musicians
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'musicians') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/musicians') }}" class="nav-link  @if ($segment2 == 'musicians') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Musicians</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Product') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'products' || $segment2 == 'auction-products') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'products') active @endif">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>
                            Products
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'products') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/products') }}" class="nav-link  @if ($segment2 == 'products') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Products </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/admin/auction-products') }}" class="nav-link  @if ($segment2 == 'auction-products') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Auction Products </p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Product Report Abuse') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'product-report-abuses') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'product-report-abuses') active @endif">
                        <i class="nav-icon fas fa-list-ul"></i>
                        <p>
                            Products Report Abuse
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'product-report-abuses') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/product-report-abuses') }}" class="nav-link  @if ($segment2 == 'product-report-abuses') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Report Abuse </p>
                            </a>
                        </li>
                        @if ($user->can('View Product Report Items') || $user->hasRole('Super Admin'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/product-report-items') }}" class="nav-link  @if ($segment2 == 'product-report-items') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p> Report Item </p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if ($user->can('View Orders') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'orders' ) menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'orders' ) open active @endif">
                        <i class="nav-icon fas fa-cart-arrow-down"></i>
                        <p>
                            Orders
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'orders') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/orders') }}" class="nav-link  @if ($segment2 == 'orders') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Orders</p>
                            </a>
                        </li>
                    
                    </ul>
                </li>
                @endif
                @if ($user->can('View Transactions') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'transactions' ) menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'transactions' ) open active @endif">
                        <i class="nav-icon fas fa-hand-holding-usd"></i>
                        <p>
                            Transactions
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'transactions') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/transactions') }}" class="nav-link  @if ($segment2 == 'transactions') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Transactions</p>
                            </a>
                        </li>
                    
                    </ul>
                </li>
                @endif
                @if ($user->can('View Bidding History') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'bidding-history' ) menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'bidding-history' ) open active @endif">
                        <i class="nav-icon fas fa-gavel"></i>
                        <p>
                            Bidding History
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'bidding-history') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/bidding-history') }}" class="nav-link  @if ($segment2 == 'bidding-history') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Bidding History</p>
                            </a>
                        </li>

                    </ul>
                </li>
                @endif

                @if ($user->can('View Ads') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'ads') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'buyers') active @endif">
                        <i class="nav-icon fas fa-ad"></i>
                        <p>
                            Ads
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'ads') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/ads') }}" class="nav-link  @if ($segment2 == 'ads') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Ads</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Reviews') || $user->hasRole('Super Admin'))

                <li class="nav-item @if ($segment2 == 'reviews') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'reviews') active @endif">
                        <i class="nav-icon fas fa-star"></i>
                        <p>
                            Reviews
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'reviews') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/reviews') }}" class="nav-link  @if ($segment2 == 'reviews') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Reviews</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View CMS Page') || $user->hasRole('Super Admin'))

                <li class="nav-item @if ($segment2 == 'cms-pages') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'cms-pages') active @endif">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            CMS Pages
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'cms-pages') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/cms-pages') }}" class="nav-link  @if ($segment2 == 'cms-pages') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage CMS pages</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Faq') || $user->can('View Faq Categories') || $user->hasRole('Super Admin'))
                <li class="nav-item  @if ($segment2 == 'faq-categories' || $segment2 == 'faqs') menu-is-opening menu-open @endif">

                    <a href="#" class="nav-link @if ($segment2 == 'faq-categories' || $segment2 == 'faqs') active @endif">
                        <i class="nav-icon far fa-question-circle"></i>
                        <p>
                            Faq
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'faq-categories' || $segment2 == 'faqs') block @endif">
                        @if ($user->can('View Faq Categories'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/faq-categories') }}" class="nav-link  @if ($segment2 == 'faq-categories') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Faq Categories</p>
                            </a>
                        </li>
                        @endif

                        @if ($user->can('View Faq'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/faqs') }}" class="nav-link  @if ($segment2 == 'faqs') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Faqs</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if ($user->can('View Blog') || $user->can('View Blog Categories') || $user->hasRole('Super Admin'))

                <li class="nav-item  @if ($segment2 == 'blog-categories' || $segment2 == 'blogs') menu-is-opening menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'blog-categories' || $segment2 == 'blogs') active @endif">
                        <i class="nav-icon fas fa-blog"></i>
                        <p>
                            Blogs
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'blog-categories' || $segment2 == 'blogs') block @endif">
                        @if ($user->can('View Blog Categories'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/blog-categories') }}" class="nav-link  @if ($segment2 == 'blog-categories') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Blog Categories</p>
                            </a>
                        </li>
                        @endif

                        @if ($user->can('View Blog'))
                        <li class="nav-item">
                            <a href="{{ url('/admin/blogs') }}" class="nav-link  @if ($segment2 == 'blogs') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Blogs</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if ($user->can('View Language') || $user->hasRole('Super Admin'))

                <li class="nav-item  @if ($segment2 == 'languages') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'languages') active @endif">
                        <i class="nav-icon fas fa-language"></i>
                        <p>
                            Languages
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'languages') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/languages') }}" class="nav-link  @if ($segment2 == 'languages') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Languages</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Video Guides') || $user->hasRole('Super Admin'))

                <li class="nav-item @if ($segment2 == 'video-guides') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'video-guides') active @endif">
                        <i class="nav-icon fab fa-youtube"></i>
                        <p>
                            Video Guides
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'video-guides') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/video-guides') }}" class="nav-link  @if ($segment2 == 'video-guides') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Video Guides</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                
                @if ($user->can('View Banner') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'banners') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'banners') active @endif">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            Banners
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'banners') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/banners') }}" class="nav-link  @if ($segment2 == 'banners') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Banners</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if ($user->can('View Contact Us Log') || $user->hasRole('Super Admin'))

                <li class="nav-item @if ($segment2 == 'contactus-log') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'contactus-log') active @endif">
                        <i class="nav-icon fas fa-history"></i>
                        <p>
                            Contact Us Log
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'contactus-log') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/contactus-log') }}" class="nav-link  @if ($segment2 == 'contactus-log') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Contact Us Log</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Event Logs') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'logs') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'logs') active @endif">
                        <i class="nav-icon fas fa-history"></i>
                        <p>
                            Admin Logs
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'logs') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/logs') }}" class="nav-link  @if ($segment2 == 'logs') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Admin Logs</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if ($user->can('View Templates') || $user->hasRole('Super Admin'))
                <li class="nav-item @if ($segment2 == 'templates') menu-open @endif">
                    <a href="#" class="nav-link @if ($segment2 == 'templates') active @endif">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            Email Templates
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="@if ($segment2 == 'templates') block @endif">
                        <li class="nav-item">
                            <a href="{{ url('/admin/templates') }}" class="nav-link  @if ($segment2 == 'templates') active @endif">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Manage Templates</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ url('/admin/logout') }}" class="nav-link">
                        <i class="fas fa-sign-out-alt nav-icon"></i>
                        <p>logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>