@extends('admin.layout')

@section('page-title', 'Manage Jobs')

@section('admin-content')

<div class="panel panel-default">
    <div class="panel-heading" style="display:flex; align-items:center; justify-content:space-between;">
        <strong><i class="ti-briefcase"></i> All Job Listings</strong>
        <a href="{{ route('post-job') }}" class="btn btn-success btn-sm">
            <i class="ti-plus"></i> Post New Job
        </a>
    </div>
    <div class="panel-body" style="padding:0;">
        <table class="table table-striped table-hover" style="margin:0;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td><strong>{{ $job->title }}</strong></td>
                    <td>{{ $job->company }}</td>
                    <td>{{ $job->location ?? '–' }}</td>
                    <td>{{ $job->category ?? '–' }}</td>
                    <td>
                        @if ($job->is_active)
                            <span class="label label-success">Active</span>
                        @else
                            <span class="label label-danger">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $job->created_at->format('d M Y') }}</td>
                    <td class="table-actions">
                        {{-- Toggle active --}}
                        <form method="POST" action="{{ route('admin.jobs.toggle', $job) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-xs {{ $job->is_active ? 'btn-warning' : 'btn-success' }}"
                                    title="{{ $job->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="ti-{{ $job->is_active ? 'close' : 'check' }}"></i>
                                {{ $job->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        {{-- Delete --}}
                        <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}"
                              onsubmit="return confirm('Delete this job permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                <i class="ti-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding: 30px;">
                        No jobs posted yet. <a href="{{ route('post-job') }}">Post the first one!</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($jobs->hasPages())
    <div class="panel-footer">
        {{ $jobs->links() }}
    </div>
    @endif
</div>

@endsection
