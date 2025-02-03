<?php
namespace App\Http\Controllers;
use App\Models\Article;
use Illuminate\Http\Request;



class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return view('articles.index', compact('articles'));
    }

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required',
            'contenu' => 'required',
        ]);

        Article::create($request->all());

        return redirect()->route('articles.index')->with('success', 'Article ajouté avec succès.');
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'titre' => 'required',
            'contenu' => 'required',
        ]);

        $article->update($request->all());

        return redirect()->route('articles.index')->with('success', 'Article mis à jour.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé.');
    }
    public function exportCSV()
    {
        $articles = Article::all();
        $fileName = 'articles.csv';

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($articles) {
            $file = fopen('php://output', 'w');
            // Ajouter l'en-tête du CSV
            fputcsv($file, ['Titre', 'Contenu']);

            // Ajouter les données des articles
            foreach ($articles as $article) {
                fputcsv($file, [$article->titre, $article->contenu]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function importCSV(Request $request)
{
    // Vérifier si un fichier a été envoyé
    if ($request->hasFile('csv_file')) {
        $file = $request->file('csv_file');

        // Ouvrir le fichier en lecture
        $handle = fopen($file->getPathname(), 'r');

        // Sauter la première ligne (en-têtes du CSV)
        fgetcsv($handle);

        // Lire chaque ligne et insérer dans la base de données
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            Article::create([
                'titre' => $data[0],   // Première colonne = titre
                'contenu' => $data[1], // Deuxième colonne = contenu
            ]);
        }

        fclose($handle);

        return back()->with('success', 'Importation réussie.');
    }

    return back()->with('error', 'Veuillez sélectionner un fichier CSV.');
}

}

