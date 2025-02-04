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
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Expires" => "0",
        ];
    
        $callback = function () use ($articles) {
            $file = fopen('php://output', 'w');
    
            // Ajouter le BOM pour la compatibilité avec Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
            // Définir le séparateur à ";"
            fputcsv($file, ['Titre', 'Contenu'], ';');
    
            foreach ($articles as $article) {
                fputcsv($file, [$article->titre, $article->contenu], ';');
            }
    
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
    }
    
    public function importCSV(Request $request)
{
    $file = $request->file('csv_file');
    set_time_limit(1200); // Augmente la limite d'exécution

    if ($file) {
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle, 1000, ';'); // Ignorer l'en-tête

        $articles = []; // Tableau pour stocker les données en mémoire
        $titresExistants = Article::pluck('titre')->toArray(); // Récupérer tous les titres existants

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $titre = $data[0];
            $contenu = $data[1];

            if (!in_array($titre, $titresExistants)) {
                $articles[] = [
                    'titre' => $titre,
                    'contenu' => $contenu,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insérer en lot toutes les 1000 lignes
            if (count($articles) >= 1000) {
                Article::insert($articles);
                $articles = []; // Réinitialiser le tableau
            }
        }

        // Insérer les données restantes
        if (!empty($articles)) {
            Article::insert($articles);
        }

        fclose($handle);
    }

    return back()->with('success', 'Importation rapide terminée sans doublons !');
}


}

