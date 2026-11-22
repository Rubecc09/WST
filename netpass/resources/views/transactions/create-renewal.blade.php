@extends('layouts.app')
@section('title', 'New renewal')
@section('breadcrumb', 'New renewal')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
@endpush
@section('content')
<div class="page-heading page-heading-compact"><div><a class="back-link" href="{{ route('transactions.index') }}"><x-icon name="arrow-left" /> Back to transactions</a><h1>New renewal</h1><p class="page-description">Create a membership request for an existing member.</p></div></div>
@include('transactions.partials.form')
@endsection
