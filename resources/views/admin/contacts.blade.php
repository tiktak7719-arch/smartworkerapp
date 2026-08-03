@extends('admin.layout')

@section('page-title', 'Contact Messages')

@section('admin-content')

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><i class="ti-email"></i> All Contact Messages</strong>
    </div>
    <div class="panel-body" style="padding:0;">
        <table class="table table-striped table-hover" style="margin:0;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contacts as $contact)
                <tr class="{{ $contact->is_read ? '' : 'warning' }}">
                    <td>{{ $contact->id }}</td>
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>
                        <a href="{{ route('admin.contacts.show', $contact) }}">
                            {{ Str::limit($contact->subject, 40) }}
                        </a>
                    </td>
                    <td>
                        @if ($contact->is_read)
                            <span class="label label-default">Read</span>
                        @else
                            <span class="label label-warning"><i class="ti-bell"></i> New</span>
                        @endif
                    </td>
                    <td>{{ $contact->created_at->format('d M Y H:i') }}</td>
                    <td class="table-actions">
                        <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-xs btn-primary">
                            <i class="ti-eye"></i> View
                        </a>
                        <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                              onsubmit="return confirm('Delete this message?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger">
                                <i class="ti-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                        No messages received yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($contacts->hasPages())
    <div class="panel-footer">
        {{ $contacts->links() }}
    </div>
    @endif
</div>

@endsection
