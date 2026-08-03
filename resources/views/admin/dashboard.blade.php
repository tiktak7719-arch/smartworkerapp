@extends('admin.layout')

@section('page-title', 'Dashboard')

@section('admin-content')

<div class="row">
    <div class="col-md-3">
        <div class="stat-card bg-primary-dark">
            <h2>{{ $totalJobs }}</h2>
            <p><i class="ti-briefcase"></i> Total Jobs</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success-dark">
            <h2>{{ $activeJobs }}</h2>
            <p><i class="ti-check"></i> Active Jobs</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning-dark">
            <h2>{{ $totalContacts }}</h2>
            <p><i class="ti-email"></i> Total Messages</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-danger-dark">
            <h2>{{ $unreadContacts }}</h2>
            <p><i class="ti-bell"></i> Unread Messages</p>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;">
    <div class="col-md-3">
        <div class="stat-card bg-info-dark">
            <h2>{{ $totalUsers }}</h2>
            <p><i class="ti-user"></i> Registered Users</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success-dark">
            <h2>{{ $verifiedUsers }}</h2>
            <p><i class="ti-check-box"></i> Verified Users</p>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 10px;">
    <div class="col-md-6"><div class="panel panel-default"><div class="panel-heading"><strong><i class="ti-bar-chart"></i> Hiring Trend Insights</strong></div><div class="panel-body">@forelse($applicationTrends as $status => $total)<div style="margin-bottom:12px;"><div class="clearfix"><span>{{ ucfirst($status) }}</span><strong class="pull-right">{{ $total }}</strong></div><div class="progress" style="height:9px;margin:4px 0 0;"><div class="progress-bar progress-bar-info" style="width:{{ min(100, $total * 10) }}%"></div></div></div>@empty<p class="text-muted">Application trends appear here as candidates apply.</p>@endforelse</div></div></div>
    <div class="col-md-6"><div class="panel panel-default"><div class="panel-heading"><strong><i class="ti-light-bulb"></i> In-Demand Categories</strong></div><div class="panel-body"><ol style="padding-left:20px;margin:0;">@forelse($topCategories as $category)<li style="padding:5px 0;">{{ $category->name }} <span class="label label-primary pull-right">{{ $category->active_jobs_count }} active jobs</span></li>@empty<li class="text-muted">No category trend data yet.</li>@endforelse</ol></div></div></div>
</div>

<div class="row" style="margin-top: 10px;">
    {{-- Recent Jobs --}}
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="ti-briefcase"></i> Recent Jobs</strong>
                <a href="{{ route('admin.jobs') }}" class="pull-right">View All</a>
            </div>
            <div class="panel-body" style="padding:0;">
                <table class="table table-striped" style="margin:0;">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Posted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentJobs as $job)
                        <tr>
                            <td>{{ Str::limit($job->title, 30) }}</td>
                            <td>{{ Str::limit($job->company, 20) }}</td>
                            <td>
                                @if ($job->is_active)
                                    <span class="label label-success">Active</span>
                                @else
                                    <span class="label label-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $job->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No jobs yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Messages --}}
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="ti-email"></i> Recent Messages</strong>
                <a href="{{ route('admin.contacts') }}" class="pull-right">View All</a>
            </div>
            <div class="panel-body" style="padding:0;">
                <table class="table table-striped" style="margin:0;">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Read</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentContacts as $contact)
                        <tr class="{{ $contact->is_read ? '' : 'warning' }}">
                            <td>{{ $contact->name }}</td>
                            <td>
                                <a href="{{ route('admin.contacts.show', $contact) }}">
                                    {{ Str::limit($contact->subject, 30) }}
                                </a>
                            </td>
                            <td>
                                @if ($contact->is_read)
                                    <span class="label label-default">Read</span>
                                @else
                                    <span class="label label-warning">New</span>
                                @endif
                            </td>
                            <td>{{ $contact->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No messages yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
