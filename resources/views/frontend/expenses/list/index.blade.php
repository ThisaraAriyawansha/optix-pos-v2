@extends('layouts.frontend')

@section('content')
    @include('frontend.expenses.list.hero')
    @include('frontend.expenses.list.filters')
    @include('frontend.expenses.list.body')
@endsection
