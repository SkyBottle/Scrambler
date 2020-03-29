<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WordController extends Controller
{
    public function index()
    {
        $file = fopen(resource_path("word-source\medium-word.txt"), "r");
        $words = '';
        while(!feof($file)) {
            $words = $words.fgets($file).',';
        }
        fclose($file);

        $words = explode(',',$words);
        $word = Arr::random($words);

        return $word;
    }
}
