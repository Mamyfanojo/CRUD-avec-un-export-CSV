@extends('layout')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Liste des Articles</h2>
    <a href="{{ route('articles.create') }}" class="btn btn-primary mb-3">Créer un article</a>
    <a href="{{ route('articles.export') }}" class="btn btn-primary">Exporter en CSV</a>
    <form action="{{ route('articles.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <input type="file" name="csv_file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Importer CSV</button>
    </form>


    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Contenu</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles as $article)
                <tr>
                    <td>{{ $article->id }}</td>
                    <td>{{ $article->titre }}</td>
                    <td>{{ Str::limit($article->contenu, 50) }}</td>
                    <td>
                        <!-- <a href="{{ route('articles.show', $article->id) }}" class="btn btn-info btn-sm">Voir</a> -->
                        <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
