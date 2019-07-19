@extends('layouts.app')
@section('content')
	<!-- <displayview :userid="{{ auth()->user()->USER_ID }}"></displayview> -->
	@php echo htmlspecialchars("Hellos><//&",ENT_QUOTES,'UTF-8');  @endphp
	<deviceoperation :userid="{{ auth()->user()->USER_ID }}"></deviceoperation>
@endsection