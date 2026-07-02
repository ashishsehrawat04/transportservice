<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Transport CRM Admin</title>

    <meta  content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    <link rel="icon" href="{{ asset('fav-icon.svg') }}" type="image/svg+xml" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Fonts and icons -->
    <script src="{{ asset('assets/admin/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons",],
             urls: ["{{ asset('assets/admin/css/fonts.min.css') }}"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/kaiadmin.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/production-admin.css') }}" />


  </head>
  <style>
  .logo-header{
    min-height:76px;
    height:76px;
    padding:0 14px;
  }

  .logo-header .logo{
    width:206px;
    height:66px;
    display:flex;
    align-items:center;
    justify-content:flex-start;
  }

  .logo-header .gs-admin-logo{
    width:206px;
    height:58px;
    max-width:none;
    object-fit:contain;
    object-position:left center;
    display:block;
  }

  .sidebar .sidebar-wrapper{
    max-height:calc(100vh - 76px);
  }

  .admin-table-scroll,
  .admin-table-scroll .dataTables_wrapper,
  .admin-table-scroll .dataTables_scroll{
    width:100%;
  }

  .admin-table-scroll .dataTables_scrollBody{
    overflow-x:auto !important;
    overflow-y:hidden;
  }

  .admin-table-scroll table{
    width:100% !important;
    min-width:900px;
  }

  .admin-table-scroll th,
  .admin-table-scroll td{
    white-space:nowrap;
  }

  .admin-table-scroll .action-buttons{
    display:inline-flex;
    align-items:center;
    gap:6px;
    flex-wrap:nowrap;
  }

  .main-header .logo-header .logo{
    width:206px;
  }

  @media (max-width:991px){
    .main-header .logo-header .logo{
        width:198px;
    }
  }

  #gsLoader{
    position:fixed;
    inset:0;
    background:radial-gradient(circle at center, rgba(20,36,32,.96), rgba(4,8,10,.98));
    backdrop-filter:blur(7px);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:999999;
  }

  .gs-wrapper{
    width:min(520px, 88vw);
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:22px;
  }

  .transport-loader-scene{
    position:relative;
    width:100%;
    height:180px;
    overflow:hidden;
  }

  .loader-skyline{
    position:absolute;
    left:8%;
    right:8%;
    bottom:76px;
    height:42px;
    background:
      linear-gradient(#26343a 0 0) 0 14px/28px 28px no-repeat,
      linear-gradient(#2f4248 0 0) 45px 0/35px 42px no-repeat,
      linear-gradient(#26343a 0 0) 100px 18px/44px 24px no-repeat,
      linear-gradient(#2f4248 0 0) 170px 6px/38px 36px no-repeat,
      linear-gradient(#26343a 0 0) 235px 20px/70px 22px no-repeat,
      linear-gradient(#2f4248 0 0) right 10px/58px 32px no-repeat;
    opacity:.7;
  }

  .loader-road{
    position:absolute;
    left:0;
    right:0;
    bottom:34px;
    height:54px;
    border-radius:999px;
    background:linear-gradient(180deg,#333b40,#161b1e);
    box-shadow:0 20px 55px rgba(0,0,0,.42), inset 0 2px 0 rgba(255,255,255,.12);
  }

  .loader-road:before{
    content:"";
    position:absolute;
    left:0;
    right:0;
    top:25px;
    height:4px;
    background:repeating-linear-gradient(90deg, #f7c948 0 44px, transparent 44px 76px);
    animation:roadMove .7s linear infinite;
  }

  .loader-truck{
    position:absolute;
    left:-210px;
    bottom:72px;
    width:178px;
    height:66px;
    animation:truckDrive 2.35s linear infinite;
  }

  .truck-box{
    position:absolute;
    left:0;
    top:12px;
    width:108px;
    height:46px;
    border-radius:8px 10px 8px 8px;
    background:linear-gradient(135deg,#18a058,#0f7f45);
    box-shadow:0 10px 22px rgba(0,0,0,.25);
  }

  .truck-cab{
    position:absolute;
    right:8px;
    top:22px;
    width:60px;
    height:36px;
    border-radius:8px 18px 8px 4px;
    background:linear-gradient(135deg,#ffb703,#f47c20);
    box-shadow:0 10px 22px rgba(0,0,0,.25);
  }

  .truck-cab:before{
    content:"";
    position:absolute;
    right:13px;
    top:7px;
    width:21px;
    height:13px;
    border-radius:4px;
    background:#c9f3ff;
  }

  .truck-light{
    position:absolute;
    right:4px;
    bottom:8px;
    width:9px;
    height:7px;
    border-radius:3px;
    background:#fff7a8;
    box-shadow:12px 0 24px rgba(255,247,168,.8);
  }

  .wheel{
    position:absolute;
    bottom:0;
    width:24px;
    height:24px;
    border-radius:50%;
    background:#070b0d;
    border:5px solid #9da8ae;
    animation:wheelSpin .55s linear infinite;
  }

  .wheel-left{ left:26px; }
  .wheel-right{ right:28px; }

  .loader-car{
    position:absolute;
    left:6%;
    bottom:60px;
    width:72px;
    height:28px;
    border-radius:20px 24px 8px 8px;
    background:#39a2ff;
    opacity:.9;
    animation:carDrive 2.1s linear infinite;
  }

  .loader-car:before{
    content:"";
    position:absolute;
    left:20px;
    top:-10px;
    width:32px;
    height:16px;
    border-radius:16px 16px 0 0;
    background:#bdefff;
  }

  .loader-car:after{
    content:"";
    position:absolute;
    left:10px;
    right:10px;
    bottom:-6px;
    height:10px;
    background:radial-gradient(circle,#111 0 5px, transparent 6px) left center/20px 10px no-repeat,
               radial-gradient(circle,#111 0 5px, transparent 6px) right center/20px 10px no-repeat;
  }

  .gs-text{
    color:#fff;
    font-size:20px;
    font-weight:700;
    letter-spacing:2px;
    text-transform:uppercase;
  }

  .gs-progress{
    width:min(380px, 76vw);
    height:7px;
    background:rgba(255,255,255,.16);
    border-radius:20px;
    overflow:hidden;
  }

  .gs-bar{
    width:45%;
    height:100%;
    border-radius:20px;
    background:linear-gradient(90deg,#18a058,#ffb703,#18a058);
    animation:loading 1.15s linear infinite;
  }

  @keyframes roadMove{
    to{ background-position:-76px 0; }
  }

  @keyframes truckDrive{
    0%{ transform:translateX(-40px) translateY(0); }
    25%{ transform:translateX(160px) translateY(-4px); }
    50%{ transform:translateX(360px) translateY(0); }
    75%{ transform:translateX(560px) translateY(-4px); }
    100%{ transform:translateX(760px) translateY(0); }
  }

  @keyframes wheelSpin{
    to{ transform:rotate(360deg); }
  }

  @keyframes carDrive{
    0%{ transform:translateX(-120px); }
    100%{ transform:translateX(560px); }
  }

  @keyframes loading{
    0%{ transform:translateX(-120%); }
    100%{ transform:translateX(230%); }
  }
    </style>

  <body>

  <script src="{{ asset('assets/admin/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/core/bootstrap.min.js') }}"></script>

    <script src="{{ asset('assets/admin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/kaiadmin.min.js') }}"></script>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="{{ route('admin.dashboard') }}" class="logo">
              <img src="{{ asset('assets/admin/img/transport-admin-logo.svg') }}" alt="Transport CRM Admin" class="navbar-brand gs-admin-logo"/>
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>


        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">

                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }} ">
                    <i class="fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                    </a>
                </li>

                 <li class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <a href="{{ route('admin.users') }} ">
                    <i class="fas fa-users"></i>
                    <p>Users</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.city_routes') ? 'active' : '' }}">
                    <a href="{{ route('admin.city_routes') }} ">
                    <i class="fas fa-road"></i>
                    <p>City Routes</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.transport_prices') ? 'active' : '' }}">
                    <a href="{{ route('admin.transport_prices') }} ">
                    <i class="fas fa-dollar-sign"></i>
                    <p>Transport Prices</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.transport_leads') || request()->routeIs('admin.manage.transport_lead') ? 'active' : '' }}">
                    <a href="{{ route('admin.transport_leads') }} ">
                    <i class="fas fa-truck"></i>
                    <p>Transport Leads</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.transport_quotes') ? 'active' : '' }}">
                    <a href="{{ route('admin.transport_quotes') }} ">
                    <i class="fas fa-file-invoice"></i>
                    <p>Transport Quotes</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                    <a href="{{ route('admin.payments') }} ">
                    <i class="fas fa-credit-card"></i>
                    <p>Payments</p>
                    </a>
                </li>

                <li class="nav-item {{ request()->routeIs('admin.auth_settings') ? 'active' : '' }}">
                    <a href="{{ route('admin.auth_settings') }} ">
                    <i class="fas fa-user-lock"></i>
                    <p>Auth Settings</p>
                    </a>
                </li>

            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="{{ route('admin.dashboard') }}" class="logo">
                <img src="{{ asset('assets/admin/img/transport-admin-logo.svg') }}" alt="Transport CRM Admin" class="navbar-brand gs-admin-logo" />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
                <div class="input-group">
                    <h2>Hi, {{ auth()->user()?->name ?? 'Admin' }}</h2>
                  <!-- <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                      <i class="fa fa-search search-icon"></i>
                    </button>
                  </div> -->
                  <!-- <input type="text" placeholder="Search ..." class="form-control"/> -->
                </div>
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"
                    role="button" aria-expanded="false"  aria-haspopup="true">  <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"  placeholder="Search ..." class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a
                    class="nav-link dropdown-toggle"  href="#" id="messageDropdown"  role="button"  data-bs-toggle="dropdown" aria-haspopup="true"  aria-expanded="false">
                    <i class="fa fa-envelope"></i>
                    @if(($adminHeader['recentLeads'] ?? collect())->count())
                      <span class="notification">{{ ($adminHeader['recentLeads'] ?? collect())->count() }}</span>
                    @endif
                  </a>
                  <ul
                    class="dropdown-menu messages-notif-box animated fadeIn"
                    aria-labelledby="messageDropdown"
                  >
                    <li>
                      <div
                        class="dropdown-title d-flex justify-content-between align-items-center"
                      >
                        Recent Leads
                        <a href="{{ route('admin.transport_leads') }}" class="small">View all</a>
                      </div>
                    </li>
                    <li>
                      <div class="message-notif-scroll scrollbar-outer">
                        <div class="notif-center">
                          @forelse(($adminHeader['recentLeads'] ?? collect()) as $lead)
                            <a href="{{ route('admin.manage.transport_lead', $lead->id) }}">
                              <div class="notif-icon notif-primary">
                                <i class="fas fa-truck"></i>
                              </div>
                              <div class="notif-content">
                                <span class="subject">{{ $lead->tracking_number ?? 'New Lead' }}</span>
                                <span class="block">
                                  {{ optional($lead->user)->name ?? 'Customer' }} -
                                  {{ optional($lead->cityRoute)->from_city ?? '-' }} to {{ optional($lead->cityRoute)->to_city ?? '-' }}
                                </span>
                                <span class="time">{{ $lead->created_at?->diffForHumans() }}</span>
                              </div>
                            </a>
                          @empty
                            <div class="px-3 py-4 text-center text-muted">No recent leads</div>
                          @endforelse
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="{{ route('admin.transport_leads') }}"
                        >See all leads<i class="fa fa-angle-right"></i>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a class="nav-link dropdown-toggle"  href="#"  id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true"  aria-expanded="false"
                  >
                    <i class="fa fa-bell"></i>
                    @if(($adminHeader['notificationCount'] ?? 0) > 0)
                      <span class="notification">{{ $adminHeader['notificationCount'] }}</span>
                    @endif
                  </a>
                  <ul
                    class="dropdown-menu notif-box animated fadeIn"
                    aria-labelledby="notifDropdown"
                  >
                    <li>
                      <div class="dropdown-title">
                        You have {{ $adminHeader['notificationCount'] ?? 0 }} CRM alerts
                      </div>
                    </li>
                    <li>
                      <div class="notif-scroll scrollbar-outer">
                        <div class="notif-center">
                          <a href="{{ route('admin.transport_leads') }}">
                            <div class="notif-icon notif-primary">
                              <i class="fas fa-truck-loading"></i>
                            </div>
                            <div class="notif-content">
                              <span class="block">{{ number_format($adminHeader['pendingLeads'] ?? 0) }} pending transport leads</span>
                              <span class="time">Needs admin review</span>
                            </div>
                          </a>
                          <a href="{{ route('admin.payments') }}">
                            <div class="notif-icon notif-success">
                              <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="notif-content">
                              <span class="block">
                                {{ number_format($adminHeader['pendingPayments'] ?? 0) }} pending payments
                              </span>
                              <span class="time">Today revenue {{ number_format($adminHeader['todayRevenue'] ?? 0, 2) }}</span>
                            </div>
                          </a>
                          <a href="{{ route('admin.users') }}">
                            <div class="notif-icon notif-warning">
                              <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="notif-content">
                              <span class="block">
                                {{ number_format($adminHeader['pendingUsers'] ?? 0) }} users pending approval
                              </span>
                              <span class="time">User CRM</span>
                            </div>
                          </a>
                          @foreach(($adminHeader['recentPayments'] ?? collect())->take(1) as $payment)
                          <a href="{{ route('admin.payments') }}">
                            <div class="notif-icon notif-secondary">
                              <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="notif-content">
                              <span class="block">Latest invoice {{ $payment->invoice_number ?? '-' }}</span>
                              <span class="time">{{ number_format($payment->amount, 2) }} - {{ ucfirst($payment->status) }}</span>
                            </div>
                          </a>
                          @endforeach
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="{{ route('admin.dashboard') }}"
                        >Open dashboard<i class="fa fa-angle-right"></i>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a  class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false"  >
                    <i class="fas fa-layer-group"></i>
                  </a>
                  <div class="dropdown-menu quick-actions animated fadeIn">
                    <div class="quick-actions-header">
                      <span class="title mb-1">Quick Actions</span>
                      <span class="subtitle op-7">{{ number_format($adminHeader['notificationCount'] ?? 0) }} active alerts</span>
                    </div>
                    <div class="quick-actions-scroll scrollbar-outer">
                      <div class="quick-actions-items">
                        <div class="row m-0">
                          <a class="col-6 col-md-4 p-0" href="{{ route('admin.manage.transport_lead') }}">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-danger rounded-circle">
                                <i class="fas fa-plus"></i>
                              </div>
                              <span class="text">New Lead</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="{{ route('admin.transport_leads') }}">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-warning rounded-circle"
                              >
                                <i class="fas fa-truck"></i>
                              </div>
                              <span class="text">Leads {{ number_format($adminHeader['pendingLeads'] ?? 0) }}</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="{{ route('admin.city_routes') }}">
                            <div class="quick-actions-item">
                              <div class="avatar-item bg-info rounded-circle">
                                <i class="fas fa-road"></i>
                              </div>
                              <span class="text">Routes</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="{{ route('admin.transport_prices') }}">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-success rounded-circle"
                              >
                                <i class="fas fa-rupee-sign"></i>
                              </div>
                              <span class="text">Prices</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="{{ route('admin.payments') }}">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-primary rounded-circle"
                              >
                                <i class="fas fa-file-invoice-dollar"></i>
                              </div>
                              <span class="text">Payments {{ number_format($adminHeader['pendingPayments'] ?? 0) }}</span>
                            </div>
                          </a>
                          <a class="col-6 col-md-4 p-0" href="{{ route('admin.users') }}">
                            <div class="quick-actions-item">
                              <div
                                class="avatar-item bg-secondary rounded-circle"
                              >
                                <i class="fas fa-users"></i>
                              </div>
                              <span class="text">Users {{ number_format($adminHeader['pendingUsers'] ?? 0) }}</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>

                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false"
                  >
                    <div class="avatar-sm">
                      <img  src="{{ asset('assets/admin/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle" />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold">{{ auth()->user()->name ?? 'Admin' }}</span>
                    </span>
                  </a>
                 <ul class="dropdown-menu dropdown-menu-end dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img  src="{{ asset('assets/admin/img/profile.jpg') }}" alt="image profile" class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                            <h4>{{ auth()->user()->name ?? 'Admin' }}</h4>
                            <p class="text-muted">{{ auth()->user()->email ?? auth()->user()->mobile ?? '-' }}</p>
                            @if(auth()->user()?->slug)
                              <a href="{{ route('admin.edit.users', auth()->user()->slug) }}" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                            @endif
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        @if(auth()->user()?->slug)
                          <a class="dropdown-item" href="{{ route('admin.edit.users', auth()->user()->slug) }}">My Profile</a>
                        @endif
                        <a class="dropdown-item" href="{{ route('admin.payments') }}">Today Revenue: {{ number_format($adminHeader['todayRevenue'] ?? 0, 2) }}</a>
                        <a class="dropdown-item" href="{{ route('admin.transport_leads') }}">Pending Leads: {{ number_format($adminHeader['pendingLeads'] ?? 0) }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('admin.auth_settings') }}">Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                          @csrf
                          <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        <div class="container">
          <div class="page-inner">
            <div
              class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">

            </div>
           <!-- yha add kro file ko  -->
             @yield('content')

          </div>
        </div>

        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <nav class="pull-left">
              <ul class="nav">
                <li class="nav-item">
                  <a class="nav-link" href="http://www.themekita.com">
                    ThemeKita
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#"> Help </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="#"> Licenses </a>
                </li>
              </ul>
            </nav>
            <div class="copyright">
              <?php echo date("Y") ?>, made with <i class="fa fa-heart heart text-danger"></i> by
              <a href="http://www.themekita.com">Ashish Sehrawat</a>
            </div>
            <!-- <div>
              Distributed by
              <a target="_blank" href="https://themewagon.com/">ThemeWagon</a>.
            </div> -->
          </div>
        </footer>
      </div>

      <!-- Custom template | don't include it in your project! -->
      <div class="custom-template">
        <div class="title">Settings</div>
        <div class="custom-content">
          <div class="switcher">
            <div class="switch-block">
              <h4>Logo Header</h4>
              <div class="btnSwitch">
                <button type="button" class="selected changeLogoHeaderColor" data-color="dark"></button>
                <button type="button" class="changeLogoHeaderColor"data-color="blue"></button>
                <button type="button" class="changeLogoHeaderColor" data-color="purple"></button>
                <button type="button"
                  class="changeLogoHeaderColor"
                  data-color="light-blue"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="green"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="orange"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="red"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="white"
                ></button>
                <br />
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="dark2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="blue2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="purple2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="light-blue2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="green2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="orange2"
                ></button>
                <button
                  type="button"
                  class="changeLogoHeaderColor"
                  data-color="red2"
                ></button>
              </div>
            </div>
            <div class="switch-block">
              <h4>Navbar Header</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="blue"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="purple"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="light-blue"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="green"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="orange"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="red"
                ></button>
                <button
                  type="button"
                  class="selected changeTopBarColor"
                  data-color="white"
                ></button>
                <br />
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="dark2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="blue2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="purple2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="light-blue2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="green2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="orange2"
                ></button>
                <button
                  type="button"
                  class="changeTopBarColor"
                  data-color="red2"
                ></button>
              </div>
            </div>
            <div class="switch-block">
              <h4>Sidebar</h4>
              <div class="btnSwitch">
                <button
                  type="button"
                  class="changeSideBarColor"
                  data-color="white"
                ></button>
                <button
                  type="button"
                  class="selected changeSideBarColor"
                  data-color="dark"
                ></button>
                <button
                  type="button"
                  class="changeSideBarColor"
                  data-color="dark2"
                ></button>
              </div>
            </div>
          </div>
        </div>
        <div class="custom-toggle">
          <i class="icon-settings"></i>
        </div>
      </div>
      <!-- End Custom template -->
    </div>

    <!-- ===== GRAMEEN SEVA LOADER START ===== -->
<div id="gsLoader">
    <div class="gs-wrapper">
        <div class="transport-loader-scene">
            <div class="loader-skyline"></div>
            <div class="loader-road"></div>
            <div class="loader-car"></div>
            <div class="loader-truck">
                <div class="truck-box"></div>
                <div class="truck-cab"></div>
                <div class="truck-light"></div>
                <div class="wheel wheel-left"></div>
                <div class="wheel wheel-right"></div>
            </div>
        </div>

        <div class="gs-text">Please Wait...</div>

        <div class="gs-progress">
            <div class="gs-bar"></div>
        </div>
    </div>
</div>
<!-- ===== GRAMEEN SEVA LOADER END ===== -->
    <!--   Core JS Files   -->
    <script src="{{ asset('assets/admin/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/admin/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/admin/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('assets/admin/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('assets/admin/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('assets/admin/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('assets/admin/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('assets/admin/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugin/jsvectormap/world.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('assets/admin/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('assets/admin/js/kaiadmin.min.js') }}"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="{{ asset('assets/admin/js/setting-demo.js') }}"></script>
    <script src="{{ asset('assets/admin/js/demo.js') }}"></script>
    <script>
      $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#177dff",
        fillColor: "rgba(23, 125, 255, 0.14)",
      });

      $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#f3545d",
        fillColor: "rgba(243, 84, 93, .14)",
      });

      $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#ffa534",
        fillColor: "rgba(255, 165, 52, .14)",
      });



    function showLoader(){
        const loader = document.getElementById("gsLoader");
        loader.style.display = "flex";
        loader.style.opacity = "1";
    }

    // Loader HIDE function
    function hideLoader(){
        const loader = document.getElementById("gsLoader");
        loader.style.opacity = "0";
        loader.style.transition = "0.4s";

        setTimeout(()=>{
            loader.style.display = "none";
        },400);
    }

    $(document).ajaxSend(function (event, xhr, settings) {
        const method = (settings.type || settings.method || "GET").toUpperCase();

        if (method !== "GET") {
            showLoader();
        }
    });

    $(document).ajaxComplete(function (event, xhr, settings) {
        const method = (settings.type || settings.method || "GET").toUpperCase();

        if (method !== "GET") {
            hideLoader();
        }
    });

    $(document).on("submit", "form", function () {
        showLoader();
    });

    $(document).on("click", "a[href]", function (event) {
        const link = this;
        const href = link.getAttribute("href") || "";
        const hasInlineConfirm = (link.getAttribute("onclick") || "").includes("confirm");
        const isModifiedClick = event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.target === "_blank";
        const shouldShowForAdminAction =
            href.includes("/admin/") &&
            (
                href.includes("/manage") ||
                href.includes("/edit/") ||
                href.includes("/delete/")
            );

        if (isModifiedClick || href === "#" || href.startsWith("javascript:") || !shouldShowForAdminAction) {
            return;
        }

        if (hasInlineConfirm) {
            return;
        }

        if (href.includes("/delete/") && !window.confirm("Are you sure?")) {
            event.preventDefault();
            return;
        }

        showLoader();
    });

    window.addEventListener("pageshow", function () {
        hideLoader();
    });

    </script>
  </body>
</html>
