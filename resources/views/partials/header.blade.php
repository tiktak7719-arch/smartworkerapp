<!-- Header Section Start -->
@php
  $currentUser = auth()->user();
  $isPagesArea = request()->routeIs('about', 'jobs.categories', 'faq', 'pricing', 'contact', 'privacy-policy', 'terms');
  $isCandidateArea = request()->routeIs('candidate.*', 'profile', 'profile.update', 'saved-jobs', 'my-applications', 'job-alerts');
  $isEmployerArea = request()->routeIs('employer.*', 'post-job', 'manage-jobs', 'manage-applications', 'company.profile', 'browse-resumes');
  $unreadNotificationCount = $currentUser ? $currentUser->notifications()->whereNull('read_at')->count() : 0;
@endphp

<div class="header">
  <div class="logo-menu">
    <nav class="navbar navbar-default main-navigation" role="navigation" data-spy="affix" data-offset-top="50">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand logo" href="{{ route('home') }}" aria-label="SmartWorker home">
            <img src="{{ asset('assets/img/logo.png') }}" alt="SmartWorker">
          </a>
        </div>

        <div class="collapse navbar-collapse" id="navbar">
          <ul class="nav navbar-nav">
            <li><a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">{{ __('app.home') }}</a></li>
            <li><a class="{{ request()->routeIs('jobs.*') ? 'active' : '' }}" href="{{ route('jobs.index') }}">{{ __('app.jobs') }}</a></li>

            <li class="drop">
              <a class="{{ $isPagesArea ? 'active' : '' }}" href="{{ route('about') }}">Pages <i class="fa fa-angle-down" aria-hidden="true"></i></a>
              <ul class="dropdown">
                <li><a href="{{ route('about') }}">About</a></li>
                <li><a href="{{ route('jobs.categories') }}">Browse Categories</a></li>
                <li><a href="{{ route('faq') }}">FAQ</a></li>
                <li><a href="{{ route('pricing') }}">Pricing</a></li>
                <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('terms') }}">Terms of Use</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
              </ul>
            </li>

            @if ($currentUser?->isCandidate())
              <li class="drop">
                <a class="{{ $isCandidateArea ? 'active' : '' }}" href="{{ $currentUser?->isCandidate() ? route('candidate.dashboard') : route('candidates.index') }}">Candidates <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                <ul class="dropdown">
                  @if ($currentUser?->isCandidate())
                    <li><a href="{{ route('candidate.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('profile') }}">My Profile</a></li>
                    <li><a href="{{ route('saved-jobs') }}">Saved Jobs</a></li>
                    <li><a href="{{ route('my-applications') }}">My Applications</a></li>
                    <li><a href="{{ route('job-alerts') }}">Job Alerts</a></li>
                  @endif
                </ul>
              </li>
            @endif

            @if ($currentUser?->isEmployer())
              <li class="drop">
                <a class="{{ $isEmployerArea ? 'active' : '' }}" href="{{ $currentUser?->isEmployer() ? route('employer.dashboard') : route('employers.index') }}">Employers <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                <ul class="dropdown">
                  @if ($currentUser?->isEmployer())
                    <li><a href="{{ route('employer.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('post-job') }}">Post a Job</a></li>
                    <li><a href="{{ route('manage-jobs') }}">Manage Jobs</a></li>
                    <li><a href="{{ route('manage-applications') }}">Applications</a></li>
                    <li><a href="{{ route('company.profile') }}">Company Profile</a></li>
                    <li><a href="{{ route('browse-resumes') }}">Browse Resumes</a></li>
                  @endif
                </ul>
              </li>
            @endif

            @if ($currentUser?->isAdmin())
              <li class="drop">
                <a href="{{ route('admin.dashboard') }}">Administration <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                <ul class="dropdown">
                  <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                  <li><a href="{{ route('admin.jobs') }}">Manage Jobs</a></li>
                  <li><a href="{{ route('admin.applications') }}">Applications</a></li>
                  <li><a href="{{ route('admin.users') }}">Users</a></li>
                  <li><a href="{{ route('admin.contacts') }}">Contact Messages</a></li>
                </ul>
              </li>
            @endif

            <li><a class="{{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">Blog</a></li>
          </ul>

          <ul class="nav navbar-nav navbar-right float-right">
            @guest
              <li class="left"><a href="{{ route('my-account', ['form' => 'register']) }}"><i class="ti-user"></i> Create Account</a></li>
              <li class="right"><a href="{{ route('my-account') }}"><i class="ti-lock"></i> Log In</a></li>
            @else
              <li><a href="{{ route('chat.index') }}"><i class="ti-comments"></i> {{ __('app.chat') }}</a></li>
              <li><a href="{{ route('notifications.index') }}"><i class="ti-bell"></i> {{ __('app.notifications') }}@if($unreadNotificationCount) <span class="label label-danger">{{ $unreadNotificationCount }}</span>@endif</a></li>
              @if ($currentUser->isEmployer())
                <li class="left"><a href="{{ route('post-job') }}"><i class="ti-pencil-alt"></i> Post a Job</a></li>
              @endif
              @php
                $dashboardRoute = $currentUser->isAdmin()
                  ? route('admin.dashboard')
                  : ($currentUser->isEmployer() ? route('employer.dashboard') : route('candidate.dashboard'));
                $accountInitial = Str::upper(Str::substr($currentUser->name, 0, 1));
              @endphp
              <li class="right drop account-menu">
                <a class="account-trigger" href="{{ $dashboardRoute }}" aria-label="Open {{ $currentUser->name }} account menu">
                  <span class="account-avatar" aria-hidden="true">{{ $accountInitial }}</span>
                  <span class="account-trigger-copy">
                    <span class="account-name">{{ $currentUser->name }}</span>
                    <span class="account-role">{{ ucfirst($currentUser->role) }}</span>
                  </span>
                  <i class="fa fa-angle-down account-chevron" aria-hidden="true"></i>
                </a>
                <ul class="dropdown account-dropdown" aria-label="Account menu">
                  <li class="account-summary">
                    <span class="account-avatar account-avatar-large" aria-hidden="true">{{ $accountInitial }}</span>
                    <span class="account-summary-copy">
                      <strong>{{ $currentUser->name }}</strong>
                      <small>{{ $currentUser->email }}</small>
                      <em>{{ ucfirst($currentUser->role) }} account</em>
                    </span>
                  </li>
                  <li class="account-divider" aria-hidden="true"></li>
                  <li><a href="{{ $dashboardRoute }}"><i class="ti-dashboard"></i> Dashboard</a></li>
                  @if (! $currentUser->isAdmin())
                    <li><a href="{{ $currentUser->isEmployer() ? route('company.profile') : route('profile') }}"><i class="ti-user"></i> Profile</a></li>
                  @endif
                  <li><a href="{{ route('change-password') }}"><i class="ti-lock"></i> Change Password</a></li>
                  <li class="account-divider" aria-hidden="true"></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button class="header-logout account-logout" type="submit"><i class="ti-power-off"></i> Logout</button>
                    </form>
                  </li>
                </ul>
              </li>
            @endguest
            <li>
              <form method="POST" action="{{ route('language.set', app()->getLocale() === 'en' ? 'ur' : 'en') }}" style="padding:12px 8px;">
                @csrf
                <button type="submit" class="btn btn-default btn-xs">{{ app()->getLocale() === 'en' ? 'اردو' : 'English' }}</button>
              </form>
            </li>
          </ul>
        </div>
      </div>

      <!-- Mobile Menu Start -->
      <ul class="wpb-mobile-menu">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('jobs.index') }}">Jobs</a></li>
        <li>
          <a href="{{ route('about') }}">Pages</a>
          <ul>
            <li><a href="{{ route('about') }}">About</a></li>
            <li><a href="{{ route('jobs.categories') }}">Browse Categories</a></li>
            <li><a href="{{ route('faq') }}">FAQ</a></li>
            <li><a href="{{ route('pricing') }}">Pricing</a></li>
            <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
            <li><a href="{{ route('terms') }}">Terms of Use</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
          </ul>
        </li>
        @if ($currentUser?->isCandidate())
          <li>
            <a href="{{ $currentUser?->isCandidate() ? route('candidate.dashboard') : route('candidates.index') }}">Candidates</a>
            <ul>
              @if ($currentUser?->isCandidate())
                <li><a href="{{ route('candidate.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('profile') }}">My Profile</a></li>
                <li><a href="{{ route('saved-jobs') }}">Saved Jobs</a></li>
                <li><a href="{{ route('my-applications') }}">My Applications</a></li>
                <li><a href="{{ route('job-alerts') }}">Job Alerts</a></li>
              @endif
            </ul>
          </li>
        @endif
        @if ($currentUser?->isEmployer())
          <li>
            <a href="{{ $currentUser?->isEmployer() ? route('employer.dashboard') : route('employers.index') }}">Employers</a>
            <ul>
              @if ($currentUser?->isEmployer())
                <li><a href="{{ route('employer.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('post-job') }}">Post a Job</a></li>
                <li><a href="{{ route('manage-jobs') }}">Manage Jobs</a></li>
                <li><a href="{{ route('manage-applications') }}">Applications</a></li>
                <li><a href="{{ route('company.profile') }}">Company Profile</a></li>
                <li><a href="{{ route('browse-resumes') }}">Browse Resumes</a></li>
              @endif
            </ul>
          </li>
        @endif
        @if ($currentUser?->isAdmin())
          <li>
            <a href="{{ route('admin.dashboard') }}">Administration</a>
            <ul>
              <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
              <li><a href="{{ route('admin.jobs') }}">Manage Jobs</a></li>
              <li><a href="{{ route('admin.applications') }}">Applications</a></li>
              <li><a href="{{ route('admin.users') }}">Users</a></li>
              <li><a href="{{ route('admin.contacts') }}">Contact Messages</a></li>
            </ul>
          </li>
        @endif
        <li><a href="{{ route('blog.index') }}">Blog</a></li>
        @guest
          <li class="btn-m"><a href="{{ route('my-account', ['form' => 'register']) }}"><i class="ti-user"></i> Create Account</a></li>
          <li class="btn-m"><a href="{{ route('my-account') }}"><i class="ti-lock"></i> Log In</a></li>
        @else
          <li>
            <a href="{{ $currentUser->isAdmin() ? route('admin.dashboard') : ($currentUser->isEmployer() ? route('employer.dashboard') : route('candidate.dashboard')) }}">My Account</a>
            <ul>
              <li><a href="{{ $currentUser->isAdmin() ? route('admin.dashboard') : ($currentUser->isEmployer() ? route('employer.dashboard') : route('candidate.dashboard')) }}"><i class="ti-dashboard"></i> Dashboard</a></li>
              @if (! $currentUser->isAdmin())
                <li><a href="{{ $currentUser->isEmployer() ? route('company.profile') : route('profile') }}"><i class="ti-user"></i> Profile</a></li>
              @endif
              <li><a href="{{ route('change-password') }}"><i class="ti-lock"></i> Change Password</a></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button class="header-logout" type="submit"><i class="ti-power-off"></i> Logout</button>
                </form>
              </li>
            </ul>
          </li>
          <li><a href="{{ route('chat.index') }}"><i class="ti-comments"></i> {{ __('app.chat') }}</a></li>
          <li><a href="{{ route('notifications.index') }}"><i class="ti-bell"></i> {{ __('app.notifications') }}</a></li>
        @endguest
      </ul>
      <!-- Mobile Menu End -->
    </nav>
  </div>
</div>
<!-- Header Section End -->
