<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show products', ['only' => ['index','show']]);
        $this->middleware('permission:create product', ['only' => ['create','store']]);
        $this->middleware('permission:update product', ['only' => ['edit','update']]);
        $this->middleware('permission:delete product', ['only' => ['destroy']]);

        $this->middleware('permission:show product categories', ['only' => ['indexCategory','viewCategory']]);
        $this->middleware('permission:create product category', ['only' => ['createCategory','storeCategory']]);
        $this->middleware('permission:update product category', ['only' => ['editCategory','updateCategory']]);
        $this->middleware('permission:delete product category', ['only' => ['destroyCategory']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('products.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCategory(Request $request)
    {
        return view('products.index-category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $id)
    {
        return view('products.view');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view('products.create');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCategory(Request $request)
    {
        return view('products.create-category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $slug)
    {
        return view('products.store');
    }

    public function edit($id, Request $request)
    {
        return view('products.edit');
    }

    public function editCategory($id, Request $request)
    {
        return view('products.edit-category');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        return view('products.update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings(Request $request)
    {
        return view('products.settings');
    }
}
