@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');">
  <div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">{{ $post->title }}</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li><a href="{{ route('blog.index') }}">Blog</a></li><li class="current">Article</li></ol></div></div>
</div>

<section id="content" class="section">
  <div class="container"><div class="row">
    <div class="col-md-8 col-md-offset-2">
      <article class="inner-box">
        @if ($post->cover_image)<img class="img-responsive" src="{{ asset('storage/' . $post->cover_image) }}" alt="{{ $post->title }}" style="width:100%;margin-bottom:25px;">@endif
        <h2>{{ $post->title }}</h2>
        <p class="text-muted">By {{ $post->author_name }} @if ($post->published_at) · {{ $post->published_at->format('d M Y') }} @endif</p>
        <div style="line-height:1.8;">{!! nl2br(e($post->body)) !!}</div>
      </article>
      @if ($relatedPosts->isNotEmpty())
        <h3 style="margin-top:35px;">More Career Advice</h3>
        <div class="row">
          @foreach ($relatedPosts as $relatedPost)
            <div class="col-sm-4"><div class="inner-box" style="min-height:160px;"><h4><a href="{{ route('blog.show', $relatedPost) }}">{{ $relatedPost->title }}</a></h4><p>{{ Str::limit($relatedPost->excerpt ?: strip_tags($relatedPost->body), 80) }}</p></div></div>
          @endforeach
        </div>
      @endif
    </div>
  </div></div>
</section>

@include('partials.footer')
@endsection
