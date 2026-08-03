@extends('layouts.app')

@section('content')
@include('partials.header')

<div class="page-header" style="background:url('{{ asset('assets/img/banner1.jpg') }}');">
  <div class="container"><div class="breadcrumb-wrapper"><h2 class="product-title">Contact Us</h2><ol class="breadcrumb"><li><a href="{{ route('home') }}">Home</a></li><li class="current">Contact</li></ol></div></div>
</div>

<section id="content" class="section"><div class="container"><div class="row">
  <div class="col-md-4">
    <h2 class="medium-title">Contact Us</h2>
    <div class="information">
      <div class="contact-datails"><div class="icon"><i class="ti-location-pin"></i></div><div class="info"><h3>Address</h3><span class="detail">SmartWorker Job Portal</span></div></div>
      <div class="contact-datails"><div class="icon"><i class="ti-mobile"></i></div><div class="info"><h3>Support</h3><span class="detail">Send us a message and our team will respond.</span></div></div>
      <div class="contact-datails"><div class="icon"><i class="ti-location-arrow"></i></div><div class="info"><h3>Email</h3><span class="detail">support@smartworker.com</span></div></div>
    </div>
  </div>
  <div class="col-md-8">
    @if (session('success'))<div class="alert alert-success"><i class="ti-check"></i> {{ session('success') }}</div>@endif
    <form class="contact-form" method="POST" action="{{ route('contact.store') }}">@csrf
      <div class="row">
        <div class="col-md-6"><div class="form-group"><input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Full Name" required>@error('name')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
        <div class="col-md-6"><div class="form-group"><input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="mail@example.com" required>@error('email')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
        <div class="col-md-12"><div class="form-group"><input class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject') }}" placeholder="Subject" required>@error('subject')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
        <div class="col-md-12"><div class="form-group"><textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="10" placeholder="Message" required>{{ old('message') }}</textarea>@error('message')<div class="text-danger">{{ $message }}</div>@enderror</div></div>
        <div class="col-md-12"><button type="submit" class="btn btn-common">Send Us</button></div>
      </div>
    </form>
  </div>
</div></div></section>

@include('partials.footer')
@endsection
