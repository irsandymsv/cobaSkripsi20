<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class stemmerController extends Controller
{
    public function index()
    {
    	$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
    	$stemmer  = $stemmerFactory->createStemmer();

    	// stem
    	$sentence = 'Perekonomian Indonesia sedang dalam pertumbuhan yang membanggakan';
    	$output   = $stemmer->stem($sentence);

    	return view('stemmer.index', ['output' => $output]);
    }
}
