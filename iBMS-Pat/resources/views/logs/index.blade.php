@extends('layouts.app')
@section('content')
	<logs :userid="{{ auth()->user()->USER_ID }}" test="test"></logs>
@endsection

