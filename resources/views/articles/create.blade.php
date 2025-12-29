@extends('layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h1 class="h3 mb-0">Создать новость</h1>
        <a class="btn btn-outline-secondary" href="{{ route('articles.index') }}">Назад</a>
    </div>

    <form method="POST" action="{{ route('articles.store') }}">
        @csrf

        @include('articles._form')

        <div class="d-flex" style="gap: .5rem;">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="{{ route('articles.index') }}" class="btn btn-light">Отмена</a>
        </div>
    </form>
@endsection
