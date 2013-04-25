<?php
//These two questions are placed together since they both use the same resources from twitter
//5. Using the Twitter API, pull and display the last 40 tweets from the 'ign' account. Use dev.twitter.com for reference.	
//&
//6. Using the results from the previous question, determine the most commonly used words. What is the scalability of this algorithm? Would this algorithm still work if you were parsing billions of tweets?

//template includes logo, and navigation menu which will appear on every page
/*
Note: using template.php
	the DOMDocument prototype has already been instanced to a global variable named $dom
	the use of $content will be to place information pertaining to the current page 
	the $title element has been declared and applied to the head but a name is still needed
	
*/ 
	include 'JSON/template.php';
	
//CHANGE THE TITLE TO THE NAME OF THE CURRENT PAGE
	$title->appendChild($dom->createTextNode("Tweets from IGN"));

//Accept context of file type xml over external site	
	$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));

	$displayNum = 40; //Try to get 40 results
	$projectNum = $displayNum; //theoretical value of the number of tweets to get since this program only returns tweets. 

	do{
		$url = 'http://api.twitter.com/1/statuses/user_timeline.xml?screen_name=ign&count='.$displayNum; //grabs all data pertaining to the tweets and returns them.
		$xml = file_get_contents($url, false, $context); //retrieving data from the web, this might take a while
		$xml = simplexml_load_string($xml); //returns data formatted into php friendly xml
		$displayNum += $displayNum - count($xml->status); //Change the amount to display to new amount if there is not enough tweets. If there are 40 tweets, then the difference is zero
	}while(count($xml->status) < $projectNum);
//At this point 40 tweets are returned as objects which have to be converted to strings for reading
		
	$tweets= $dom->createElement("div");
	$tweets->setAttribute('id', 'tweets');
	$frag= $dom->createDocumentFragment();
//Parsing patterns to remove links and anything else that is not an alphabet or apostrophe 
	$httpEsc= "/http\S+/";
	$alphaEsc= "/[^a-z\s']/";
	$dict= array();

//two things need to be done in this for loop. Reconstruct tweets from IGN. Parse tweets and return commonly used words
	for ($i=0; $i<$projectNum; $i++){ 
		$string= $xml->status[$i]->text->__toString();
		
		//Reconstruct Tweets from IGN
		$p= $dom->createElement('p');
		$p->appendChild($dom->createTextNode($string));
		$frag->appendChild($p);
		
		//Parse Tweets and return commonly used words
		$text = preg_replace($httpEsc, '', strtolower($string));
		$text = preg_replace($alphaEsc, '', $text);
		$text = explode(' ' , $text);
		$text = array_filter(array_map('trim', $text));
		//PHP associative arrays are of type: ordered arrays, further explanation can be found in JSON
		foreach($text as $word){
				if(array_key_exists($word, $dict)){
					$dict[$word]++;
				}else{
					$dict[$word]=1;
				}
			}
	}
	$h2= $dom->createElement('h2');
	$h2->appendChild($dom->createTextNode("Tweets from IGN"));
	$tweets->appendChild($h2);
	$tweets->appendChild($frag);
	$content->appendChild($tweets);
//Tweets have been added to the content divider

//Display the 25 most commonly used words
	$disp= 25;
	$common= $dom->createElement("div");
	$common->setAttribute("id", "commonWords");
	$ol= $dom->createElement("ol");
	$frag= $dom->createDocumentFragment();
	
	for($i=0; $i<$disp; $i++){ 
		$value = max($dict);//search through the array value to locate the highest integer
		$key= array_search($value, $dict); //search for the key associated with the value
		
		$li= $dom->createElement("li");
		$li->nodeValue = "'".$key."' used ".$value." times";
		$frag->appendChild($li);
		
		unset($dict[$key]); //remove index by key so new max value can be found
	}
	$ol->appendChild($frag);
	$h2= $dom->createElement('h2');
	$h2->appendChild($dom->createTextNode("Word ranking"));
	
	$common->appendChild($h2);
	$common->appendChild($ol);
	
	$content->appendChild($common);

// Create a clear div to allow ranking and tweets to be side by side
	$div= $dom->createElement("div");
	$div->setAttribute("id", "clear");
	$content->appendChild($div);
	
// Explaination to scalability of algorithm
	$file = file_get_contents('JSON/commonWord.json', FILE_USE_INCLUDE_PATH); 
	$str= utf8_encode($file);
	$json= json_decode($str, true);

	$h3= $dom->createElement("h3");
	$h3->appendChild($dom->createTextNode($json["question"]));
	$content->appendChild($h3);
	
	$frag= $dom->createDocumentFragment();
	foreach($json["answer"] as $paragraph){
		$p= $dom->createElement("p");
		$p->appendChild($dom->createTextNode($paragraph));
		$frag->appendChild($p);
	}
	
	$content->appendChild($frag);
	
	echo $dom->saveHTML();
	
//Adding javascript to page
	echo "<script type='text/javascript' src='script/style.js'></script>";
?>