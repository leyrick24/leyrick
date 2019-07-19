@extends('layouts.app')
@section('content')
	<about :user="{{ auth()->user() }}"></about>
@endsection

