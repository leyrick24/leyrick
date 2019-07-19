@extends('layouts.app')
@section('content')
	<help :user="{{ auth()->user() }}"></help>
@endsection

