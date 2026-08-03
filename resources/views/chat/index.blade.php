@extends('layouts.app')

@section('content')
@include('partials.header')
<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">{{ __('app.chat') }}</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">{{ __('app.home') }}</a></li><li class="current">{{ __('app.chat') }}</li></ol></div></div></div>

<section class="section"><div class="container"><div class="row"><div class="col-md-9 col-md-offset-1"><div class="inner-box"><h3>Conversations</h3>
  @forelse($conversations as $conversation)
    @php($otherParty = $conversation->otherParty(auth()->user()))
    <a href="{{ route('chat.show', $conversation) }}" class="media" style="display:block;padding:16px 4px;border-bottom:1px solid #eee;color:inherit;text-decoration:none;">
      <div class="media-left"><span class="btn btn-common btn-sm" style="border-radius:50%;cursor:default;"><i class="ti-user"></i></span></div>
      <div class="media-body"><h4 class="media-heading">{{ $otherParty->name }} <small>{{ $conversation->job ? ' - '.$conversation->job->title : '' }}</small></h4><p class="text-muted" style="margin:0;">{{ $conversation->last_message_at?->diffForHumans() ?: 'No messages yet' }}</p></div>
      <div class="media-right"><i class="ti-angle-right"></i></div>
    </a>
  @empty
    <div class="alert alert-info text-center">No conversations yet. Candidates can start a secure chat from a job listing.</div>
  @endforelse
  @if($conversations->hasPages())<div class="text-center" style="margin-top:20px;">{{ $conversations->links() }}</div>@endif
</div></div></div></div></section>
@include('partials.footer')
@endsection
