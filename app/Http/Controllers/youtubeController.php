<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client; 
use Google_Service_YouTube;

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

class youtubeController extends Controller
{
	/**
 	 * Sample PHP code for youtube.search.list
 	 * See instructions for running these code samples locally:
 	 * https://developers.google.com/explorer-help/guides/code_samples#php
 	*/

	protected $client;
	protected $service;
	protected $stemmer;

	public function __construct()
   {
      $this->client = new Google_Client();
    	$this->client->setApplicationName('API code samples');
    	$this->client->setDeveloperKey('AIzaSyCvKp8kILDK1biQ61Bdzg5eTqy7eELcw2E'); 
    	// Define service object for making API requests.
    	$this->service = new Google_Service_YouTube($this->client);

    	//Stemmer
    	$stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
    	$this->stemmer  = $stemmerFactory->createStemmer();
   }

	public function searchVideo()
	{
    	$queryParams = [
         'maxResults' => 2,
    	   'order' => 'viewCount',
    	   'publishedBefore' => '2020-02-01T20:00:00.000Z',
    	   'q' => 'bakso',
    	   'type' => 'video'
    	];

    	$response = $this->service->search->listSearch('snippet', $queryParams);
    	// dd($response);

    	foreach ($response['modelData']['items'] as $item) {
    		echo "judul: <a href=https://www.youtube.com/watch?v=".$item['id']['videoId'].">".$item['snippet']['title']."</a>"."<br>";
    		echo "nama channel: ".$item['snippet']['channelTitle']."<br>";
    		echo "Comments:"."<br>";
    		try {
    			$this->getComment($item['id']['videoId']);
    		} catch (\Google_Service_Exception $e) {
    			continue;
    		}
    		echo "======================================================================="."<br><br>";
    	}
   }

   public function getComment($videoId = '_VB39Jo8mAQ')
   {
   	$queryParams = [
   		'maxResults' => 1,
   		'moderationStatus' => 'published',
   		'order' => 'time',
   		'textFormat' => 'plainText',
   		'videoId' => $videoId
   	];

   	
   	$response = $this->service->commentThreads->listCommentThreads('snippet,replies', $queryParams);

   	foreach ($response['modelData']['items'] as $item) {
   		$nama_user = $item['snippet']['topLevelComment']['snippet']['authorDisplayName'];
   		$komentar = $item['snippet']['topLevelComment']['snippet']['textOriginal'];
   		$komen_stem = $this->stemmer->stem($komentar);

   		echo "*"."<br>";
    		echo "nama: ".$nama_user."<br>";
    		echo "komen: ".$komentar."<br>";
    		echo "komen stemmer: ".$komen_stem."<br>";
    		// echo "jml balasan: ".$item['snippet']['totalReplyCount']."<br>";
    		echo "<br>";
    	}
   }
}
