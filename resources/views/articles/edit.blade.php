@extends('layout')

@section('content')
<div class="container mt-5">
    <h2>Modifier l'Article</h2>
    <form action="{{ route('articles.update', $article->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="titre" class="form-control" value="{{ $article->titre }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contenu</label>
            <textarea name="contenu" class="form-control" required>{{ $article->contenu }}</textarea>
        </div>
        <button type="submit" class="btn btn-warning">Mettre à jour</button>
        <a href="{{ route('articles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
