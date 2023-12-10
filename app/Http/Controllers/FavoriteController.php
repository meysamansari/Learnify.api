<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(){
        $favorites = Favorite::get();
        return response()->json(['favorites' => $favorites]);
    }
}
