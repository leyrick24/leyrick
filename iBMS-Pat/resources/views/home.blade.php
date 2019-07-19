@extends('layouts.app')
@section('content')
<dashboard :userid="{{ auth()->user()->USER_ID }}"></dashboard>
@endsection
