<?php
//2. How many gamers are in the San Francisco Bay Area? Describe each step in your thought process.

//template includes logo, and navigation menu which will appear on every page
/*
Note: using template.php
	the DOMDocument prototype has already been instanced to a global variable named $dom
	the use of $content will be to place information pertaining to the current page 
	the $title element has been declared and applied to the head but a name is still needed
	
*/ 
	include 'JSON/template.php';
	
//CHANGE THE TITLE TO THE NAME OF THE CURRENT PAGE
	$title->appendChild($dom->createTextNode("Gamers in San Francisco Bay Area"));
	
//Obtaining information about me from JSON directory
//census json contains a short summary to explain how many gamers there are in the San Francisco Bay Area
	$file = file_get_contents('JSON/census.json', FILE_USE_INCLUDE_PATH); 
	$str= utf8_encode($file);
	$json= json_decode($str, true);
	
	$h2= $dom->createElement("h2");
	$h2->appendChild($dom->createTextNode($json["question"]));
	$content->appendChild($h2);
	
	/*
	$answerSet or simply, $aSet, has the following associative keys
		[title],
		[image],
		[caption],
		[content],
	*/
	$frag= $dom->createDocumentFragment();
	
	foreach($json["answer"] as $aSet){
		//Every paragraph begins with a topic
		$topic= $dom->createElement("h3");
		$topic->appendChild($dom->createTextNode($aSet["topic"]));
		$frag->appendChild($topic);
		if($aSet['image']){
			//An appropriate image is added to make each paragraph seem shorter
			$fig= $dom->createElement("figure");
			$img= $dom->createElement("img");
			$img->setAttribute('width', '400');
			$img->setAttribute('height', 'auto');
			$img->setAttribute('src', $aSet['image']);
			$fig->appendChild($img);
			
			//Addtion of caption for each image available
			$cap= $dom->createElement("figcaption");
			$cap->appendChild($dom->createTextNode($aSet['caption']));
			$fig->appendChild($cap);
			
			$frag->appendChild($fig);
		}
		//Cannot rename the content as $content as $content is being used by the div
		foreach($aSet["content"] as $summary){
		$p = $dom->createElement("p");
		$p->appendChild($dom->createTextNode($summary));
		$frag->appendChild($p);
		}
	}
	$content->appendChild($frag);

//One last foreach loop for the references used
	$h3= $dom->createElement("h3");
	$h3->nodeValue = "References";
	
//Frags only need to be instantiated once and reused many times over
	foreach($json["reference"] as $place=>$reference){
		$p= $dom->createElement("p");
		$a= $dom->createElement("a");
		$a->setAttribute("href", $reference);
		$a->nodeValue= $place;
		$p->appendChild($a);
		$frag->appendChild($p);
	}
	
	$content->appendChild($h3);
	$content->appendChild($frag);
	
//Contact information for twitter
	$a= $dom->createElement('a');
	$a->setAttribute('href', "https://twitter.com/curiousBusyBee");
	$a->appendChild($dom->createTextNode("Find me on Twitter!"));
	$content->appendChild($a);
	
//Full page is appended to the dom, thus ending DOM editing
	echo $dom->saveHTML();
	
//Adding javascript to page
	echo "<script type='text/javascript' src='script/style.js'></script>";
?>