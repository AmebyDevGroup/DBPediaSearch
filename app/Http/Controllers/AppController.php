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
            $item['types'] = $item['types']!=''?explode(',', $item['types']):[];
            return $item;
        })->toArray();

        return response()->json(array_values($response_data));
    }

    public function getSparqlNamespaces()
    {
        \EasyRdf\RdfNamespace::set('dbc', 'http://dbpedia.org/resource/Category:');
        \EasyRdf\RdfNamespace::set('dbpedia', 'http://dbpedia.org/resource/');
        \EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
        \EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');

        return response()->json(\EasyRdf\RdfNamespace::namespaces());
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
                $request->input('data.query', '')
            );
            $response['status'] = 'success';
            $data = collect((array)$result)->map(function($value) {
                $value->url = $value->url->getUri();
                $value->label = $value->label->getValue() . '(lang: ' . $value->label->getLang() . ')';
                return $value;
                })->toArray();
            $response['data'] = $data;
        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function getRdfData(Request $request)
    {
        $uri = str_replace('\resource\\', '\data\\',$request->input('data.uri', '')).'.rdf';
        $foaf = new \App\src\EasyRdf\Graph($uri);
        $foaf->load();

        return response()->json(['data' => $foaf->dump('html')]);
    }
}
