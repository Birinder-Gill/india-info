<?php

namespace App\Http\Controllers;

use App\Jobs\SeedCategories;
use App\Models\CategoryName;
use App\Models\PaginationLog;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CategoryNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function fillListings(Request $request) {
            SeedCategories::dispatch();
            return 'Dispatching job';
    }

    function getCatergories(Request $request) {
        $ids = CategoryName::first();
        dd($ids);
    }



}
