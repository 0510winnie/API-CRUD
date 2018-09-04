<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Resources\Article as ArticleResource;
use App\Article;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->paginate(5);

        //return collection of articles as a resource
        //use collection to return a list of resources
        return ArticleResource::collection($articles);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //check method: if is put request, we need to include id field,
        //else we create a new article
        $article = $request->isMethod('put') ? Article::findOrFail($request->id) : new Article;

        $article->id = $request->input('id');
        $article->title = $request->input('title');
        $article->body = $request->input('body');

        if($article->save())
        {
            return new ArticleResource($article);
        }
        //it gives back the updated article

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //get article
        $article = Article::findOrFail($id);

        //Return a single article as a resource;
        // in RESTful api, we percieve data as a resource, and do CRUD on it in accordance to HTTP methods
        return new ArticleResource($article);
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //get article
        $article = Article::findOrFail($id);

        if($article->delete())
        {
            return new ArticleResource($article);
        }
        //it gives back the deleted article
    }
}
