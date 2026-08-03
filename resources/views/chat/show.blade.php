@extends('layouts.app')

@section('content')
@include('partials.header')
@php($otherParty = $conversation->otherParty(auth()->user()))
<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');"><div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">Chat with {{ $otherParty->name }}</h2><ol class="breadcrumb"><li><a href="{{ route('chat.index') }}">Chat</a></li><li class="current">Conversation</li></ol></div></div></div>

<section class="section"><div class="container"><div class="row"><div class="col-md-9 col-md-offset-1"><div class="inner-box">
  @if($conversation->job)<div class="alert alert-info"><i class="ti-briefcase"></i> Discussing: <a href="{{ route('jobs.show', $conversation->job->slug) }}">{{ $conversation->job->title }}</a></div>@endif
  @if($otherParty->phone)<p class="text-right"><a class="btn btn-default btn-sm" href="tel:{{ $otherParty->phone }}"><i class="ti-mobile"></i> Call {{ $otherParty->name }}</a></p>@endif
  <div id="chat-messages" style="height:420px;overflow-y:auto;padding:10px;background:#fafafa;border:1px solid #eee;">
    @forelse($conversation->messages as $message)
      <div class="clearfix" style="margin:12px 0;"><div style="max-width:76%;padding:12px 14px;border-radius:10px;{{ $message->sender_id === auth()->id() ? 'float:right;background:#e84646;color:white;' : 'float:left;background:#e9eef3;color:#333;' }}"><strong style="font-size:12px;display:block;margin-bottom:4px;">{{ $message->sender->name }}</strong><span>{{ $message->body }}</span><small style="display:block;margin-top:5px;opacity:.75;">{{ $message->created_at->format('h:i A') }}</small></div></div>
    @empty
      <p class="text-center text-muted" style="padding-top:150px;">Say hello and start the conversation.</p>
    @endforelse
  </div>
  <form method="POST" action="{{ route('chat.store', $conversation) }}" style="margin-top:16px;">@csrf<div class="form-group"><label class="sr-only" for="chat-body">Message</label><textarea id="chat-body" name="body" class="form-control" rows="3" maxlength="2000" required placeholder="Write a message..."></textarea>@error('body')<small class="text-danger">{{ $message }}</small>@enderror</div><button type="submit" class="btn btn-common"><i class="ti-location-arrow"></i> Send message</button><a href="{{ route('chat.index') }}" class="btn btn-default">Back to chats</a></form>
</div></div></div></div></section>
@push('scripts')<script>document.getElementById('chat-messages').scrollTop = document.getElementById('chat-messages').scrollHeight; setTimeout(function () { window.location.reload(); }, 30000);</script>@endpush
@include('partials.footer')
@endsection
