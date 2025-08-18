  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" href="#">
          <p>AdminPanel</p>
        </a>
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-success navbar-badge" id="notification-count">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header" id="notification-header">0 Notifications</span>
          <div id="notification-items">
            <!-- Notifications will be injected here -->
          </div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>      
      <li class="nav-item">
        <a class="nav-link" href="{{route('welcome')}}" title="Homepage" target="_blank">
          <i class="fas fa-home"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{route('auth.logout')}}" title="Logout">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Fullscreen">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>      
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><b>Admin</b>Panel</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <a class="image" href="#" title="View Profile">
          <img src="{{asset(Auth::user()->profile_picture ? 'storage/profile/' . Auth::user()->profile_picture : 'images/profile.jpg')}}" class="img-circle profile-user-img" alt="User Image">
        </a>
        <div class="info">
          <a href="#" class="d-block" title="View Profile" style="font-size: 12px;">{{ Auth::user()->name ?? 'Juan Dela Cruz'}}</a>
          <a href="#" title="View Profile"><small>Administrator</small></a>
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
            <li class="nav-item">
                <a href="{{ route('dashboard.index') }}" class="nav-link @if(Route::currentRouteName() == 'dashboard.index') active @endif">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                    Dashboard
                  </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('profile.index') }}" class="nav-link @if(Route::currentRouteName() == 'profile.index') active @endif">
                  <i class="nav-icon fas fa-user-cog"></i>
                  <p>
                    Profile
                  </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('examination.index') }}" class="nav-link @if(Route::currentRouteName() == 'examination.index') active @endif">
                  <i class="nav-icon fas fa-cubes"></i>
                  <p>
                    Examination
                  </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('questionnaire.index') }}" class="nav-link @if(Route::currentRouteName() == 'questionnaire.index') active @endif">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    Questionnaire
                  </p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('results.index') }}" class="nav-link @if(Route::currentRouteName() == 'results.index') active @endif">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    Results
                  </p>
                </a>
            </li>

            <li class="nav-item d-none">
                <a href="{{ route('resultss.auto.index') }}" class="nav-link @if(Route::currentRouteName() == 'resultss.auto.index') active @endif">
                  <i class="nav-icon fas fa-user-graduate"></i>
                  <p>
                    Results Automation
                  </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('tokens.index') }}" class="nav-link @if(Route::currentRouteName() == 'tokens.index') active @endif">
                  <i class="nav-icon fas fa-key"></i>
                  <p>
                    Token Management
                  </p>
                </a>
            </li>

            <li class="nav-item @if(Route::currentRouteName() == 'analysis.question.index') menu-open @endif">
              <a href="#" class="nav-link @if(Route::currentRouteName() == 'analysis.question.index') active @endif">
                  <i class="nav-icon fas fa-chart-line"></i>
                  <p>
                      Assessment Analysis
                      <i class="fas fa-angle-left right"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('analysis.question.index') }}" class="nav-link @if(Route::currentRouteName() == 'analysis.question.index') active @endif">
                        <i class="nav-icon fas fa-tools ml-3"></i>
                        <p>Question Analysis</p>
                    </a>
                </li>
              </ul>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
</aside>