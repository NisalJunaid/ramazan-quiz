@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">Admin</h1>
    <ul class="mt-4 list-disc pl-5 text-gray-700">
        <li><a class="text-blue-600 hover:underline" href="{{ route('admin.quizzes.index') }}">Manage Quizzes</a></li>
    </ul>
    <p class="mt-4"><a class="text-blue-600 hover:underline" href="{{ route('home') }}">Back to Home</a></p>
@endsection
