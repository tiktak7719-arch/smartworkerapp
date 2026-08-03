@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">{{ __('app.notifications') }}</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">{{ __('app.home') }}</a></li><li class="current">{{ __('app.notifications') }}</li></ol></div></div></div>

<section class="section"><div class="container"><div class="row"><div class="col-md-9 col-md-offset-1">
  <div class="inner-box">
    <div class="clearfix" style="margin-bottom:20px;"><h3 class="pull-left" style="margin:0;">Your alerts</h3><form class="pull-right" method="POST" action="{{ route('notifications.read-all') }}">@csrf @method('PATCH')<button class="btn btn-default btn-sm" type="submit">Mark all as read</button></form></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @forelse($notifications as $notification)
      <div class="media" style="padding:16px 0;border-bottom:1px solid #eee;{{ $notification->read_at ? '' : 'background:#f8fbff;' }}">
        <div class="media-left"><span class="btn btn-common btn-sm" style="border-radius:50%;cursor:default;"><i class="{{ $notification->type === 'chat_message' ? 'ti-comments' : ($notification->type === 'rating_received' ? 'ti-star' : ($notification->type === 'new_application' ? 'ti-email' : ($notification->type === 'application_status' ? 'ti-check-box' : 'ti-bell'))) }}"></i></span></div>
        <div class="media-body"><h4 class="media-heading">{{ $notification->title }} @if(!$notification->read_at)<span class="label label-danger">New</span>@endif</h4><p style="margin:0 0 5px;">{{ $notification->body }}</p><small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small></div>
        <div class="media-right"><form method="POST" action="{{ route('notifications.read', $notification) }}">@csrf @method('PATCH')<button class="btn btn-default btn-sm" type="submit">Open</button></form></div>
      </div>
    @empty
      <div class="alert alert-info text-center" style="margin:0;">No alerts yet. Complete your profile and skills to receive smart job matches.</div>
    @endforelse
    @if($notifications->hasPages())<div class="text-center" style="margin-top:20px;">{{ $notifications->links() }}</div>@endif
  </div>
</div></div></div></section>

@include('partials.footer')
@endsection
