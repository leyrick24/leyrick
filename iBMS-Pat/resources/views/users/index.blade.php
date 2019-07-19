@extends('layouts.app')
@section('content')
	<users :user="{{ auth()->user() }}"></users>
@endsection

