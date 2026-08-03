@extends('admin.layout')
@section('page-title', 'All Applications')
@section('admin-content')

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="ti-briefcase"></i> All Job Applications</strong>
    </div>
    <div class="panel-body" style="padding:0;">
        <table class="table table-striped table-hover" style="margin:0;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Applicant</th>
                    <th>Job</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Applied</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td>{{ $app->id }}</td>
                    <td>{{ $app->applicant->name ?? 'N/A' }}<br>
                        <small class="text-muted">{{ $app->applicant->email ?? '' }}</small>
                    </td>
                    <td>{{ Str::limit($app->job->title ?? 'N/A', 35) }}</td>
                    <td>{{ $app->job->company ?? 'N/A' }}</td>
                    <td>{!! $app->status_badge !!}</td>
                    <td>{{ $app->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted" style="padding:30px;">No applications yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div class="panel-footer">{{ $applications->links() }}</div>
    @endif
</div>

@endsection
