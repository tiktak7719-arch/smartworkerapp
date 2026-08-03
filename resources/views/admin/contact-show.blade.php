@extends('admin.layout')

@section('page-title', 'View Message')

@section('admin-content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading" style="display:flex; justify-content:space-between; align-items:center;">
                <strong><i class="ti-email"></i> Message from {{ $contact->name }}</strong>
                <a href="{{ route('admin.contacts') }}" class="btn btn-default btn-sm">
                    <i class="ti-arrow-left"></i> Back to Messages
                </a>
            </div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="150">From</th>
                        <td>{{ $contact->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td>{{ $contact->subject }}</td>
                    </tr>
                    <tr>
                        <th>Received</th>
                        <td>{{ $contact->created_at->format('d M Y \a\t H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Message</th>
                        <td style="white-space: pre-wrap;">{{ $contact->message }}</td>
                    </tr>
                </table>

                <div style="margin-top: 15px;">
                    <a href="mailto:{{ $contact->email }}?subject=Re: {{ urlencode($contact->subject) }}"
                       class="btn btn-success">
                        <i class="ti-email"></i> Reply via Email
                    </a>
                    <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                          onsubmit="return confirm('Delete this message?')"
                          style="display:inline-block; margin-left:10px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="ti-trash"></i> Delete Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
