@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');">
  <div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">{{ $pageTitle }}</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">{{ $pageTitle }}</li></ol></div></div>
</div>

<section class="section"><div class="container"><div class="row"><div class="col-md-8 col-md-offset-2"><div class="inner-box">
  <h2 style="margin-top:0;">{{ $heading }}</h2>
  <p class="text-muted">Last updated: {{ $lastUpdated }}</p>
  @foreach ($sections as $section)
    <div style="margin-top:24px;">
      <h4>{{ $section['title'] }}</h4>
      <p style="line-height:1.8;">{{ $section['body'] }}</p>
    </div>
  @endforeach
  <a href="{{ route('contact') }}" class="btn btn-common" style="margin-top:15px;">Contact SmartWorker</a>
</div></div></div></div></section>

@include('partials.footer')
@endsection
