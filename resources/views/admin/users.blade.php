@extends('admin.layout')
@section('page-title', 'Manage Users')
@section('admin-content')

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="ti-user"></i> All Users</strong>
    </div>
    <div class="panel-body" style="padding:0;">
        <table class="table table-striped table-hover" style="margin:0;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Verification</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->isAdmin())
                            <span class="label label-danger">Admin</span>
                        @elseif($user->isEmployer())
                            <span class="label label-primary">Employer</span>
                        @else
                            <span class="label label-info">Candidate</span>
                        @endif
                    </td>
                    <td><span class="label {{ $user->is_verified ? 'label-success' : 'label-default' }}">{{ $user->is_verified ? 'Verified' : 'Pending' }}</span></td>
                    <td>
                        @if($user->is_active)
                            <span class="label label-success">Active</span>
                        @else
                            <span class="label label-danger">Suspended</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td class="table-actions">
                        @if(!$user->isAdmin())
                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-xs {{ $user->is_active ? 'btn-warning' : 'btn-success' }}">
                                {{ $user->is_active ? 'Suspend' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-xs {{ $user->is_verified ? 'btn-default' : 'btn-primary' }}">
                                {{ $user->is_verified ? 'Unverify' : 'Verify' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted" style="padding:30px;">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="panel-footer">{{ $users->links() }}</div>
    @endif
</div>

@endsection
