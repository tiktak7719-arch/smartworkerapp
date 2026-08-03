@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');">
  <div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">{{ $pageTitle }}</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">{{ $pageTitle }}</li></ol></div></div>
</div>

<section class="section"><div class="container"><div class="row"><div class="col-md-8 col-md-offset-2"><div class="inner-box text-center"><h2>{{ $heading }}</h2><p style="font-size:16px;line-height:1.8;margin-top:20px;">{{ $content }}</p><a href="{{ route('contact') }}" class="btn btn-common" style="margin-top:15px;">Contact Us</a></div></div></div></div></section>

@include('partials.footer')
@endsection
