<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Web64\LaravelNlp\Facades\NLP;

class AppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('main');
    }

    public function getSpotlightData(Request $request)
    {
        $response = Http::post(env('SPOTLIGHT_URL', 'http://178.216.200.239:5000'), [
            'data' => $request->input('data.data', '')
        ]);

        $response_data = collect($response->json())->unique('URI')->map(function($item) {
            $item['types'] = explode(',', $item['types']);
            return $item;
        });

        return response()->json($response_data);
    }

    public function getSparqlData(Request $request)
    {
        \EasyRdf\RdfNamespace::set('dbc', 'http://dbpedia.org/resource/Category:');
        \EasyRdf\RdfNamespace::set('dbpedia', 'http://dbpedia.org/resource/');
        \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
        \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');
        $sparql = new \EasyRdf\Sparql\Client('http://dbpedia.org/sparql');
        try {
            $result = $sparql->query(
                'SELECT * WHERE {'.
                '  ?url rdf:type dbo:Country .'.
                '  ?url rdfs:label ?label .'.
                '  ?url dct:subject dbc:Member_states_of_the_United_Nations .'.
                '  FILTER ( lang(?label) = "en" )'.
                '} ORDER BY ?label'
            );
        } catch (Exception $e) {
            print "<div class='error'>".$e->getMessage()."</div>\n";
        }
    }

    public function getRdfData(Request $request)
    {
        $uri = $request->input('data.uri', '');
        $foaf = new \App\src\EasyRdf\Graph($uri);
        $foaf->load();

        return response()->json(['data' => $foaf->dump('html')]);
    }
}
