@extends('layouts.app')

@section('title', $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <article class="max-w-3xl mx-auto px-4 py-16">
        <h1 class="font-display text-3xl font-extrabold mb-6">{{ $page->title }}</h1>
        <div class="prose max-w-none text-gray-700">{!! $page->body !!}</div>
    </article>
@endsection
