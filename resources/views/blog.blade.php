@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');">
  <div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">Career Blog</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">Blog</li></ol></div></div>
</div>

<section id="content" class="section">
  <div class="container">
    <div class="row">
      @forelse ($posts as $index => $post)
        <div class="col-md-4 col-sm-6 col-xs-12">
          <article class="featured-item" style="margin-bottom:30px;">
            <div class="featured-wrap"><div class="featured-inner">
              <figure class="item-thumb"><a href="{{ route('blog.show', $post) }}"><img src="{{ $post->cover_image ? asset('storage/' . $post->cover_image) : asset('assets/img/blog/blog' . (($index % 3) + 1) . '.jpg') }}" alt="{{ $post->title }}"></a></figure>
              <div class="item-body">
                <h3 class="job-title"><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                <p class="text-muted"><i class="ti-user"></i> {{ $post->author_name }} @if ($post->published_at) · <i class="ti-calendar"></i> {{ $post->published_at->format('d M Y') }} @endif</p>
                <p>{{ Str::limit($post->excerpt ?: strip_tags($post->body), 150) }}</p>
                <a href="{{ route('blog.show', $post) }}" class="btn btn-common btn-sm">Read More</a>
              </div>
            </div></div>
          </article>
        </div>
      @empty
        <div class="col-xs-12"><div class="alert alert-info text-center">No articles have been published yet.</div></div>
      @endforelse
    </div>
    @if ($posts->hasPages())<div class="text-center">{{ $posts->links() }}</div>@endif
  </div>
</section>

@include('partials.footer')
@endsection
