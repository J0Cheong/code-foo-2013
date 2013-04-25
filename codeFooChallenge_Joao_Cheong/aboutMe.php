<?php
//1. Create a 2-5 minute video introducing yourself and showing your passion for IGN and the Code-Foo program.

//template includes logo, and navigation menu which will appear on every page
/*
Note: using template.php
	the DOMDocument prototype has already been instanced to a global variable named $dom
	the use of $content will be to place information pertaining to the current page 
	the $title element has been declared and applied to the head but a name is still needed
	
*/ 
	include 'JSON/template.php';
	
//CHANGE THE TITLE TO THE NAME OF THE CURRENT PAGE
	$title->appendChild($dom->createTextNode("About Me"));
	
//Obtaining information about me from JSON directory
	$file = file_get_contents('JSON/aboutMe.json', FILE_USE_INCLUDE_PATH); 
	$str= utf8_encode($file);
	$json= json_decode($str, true);
		
	//Create an iframe for video applications
		$iframe= $dom->createElement('iframe');
		$iframe->setAttribute('width','560');
		$iframe->setAttribute('height','315');
		$iframe->setAttribute('src', "http://www.youtube.com/embed/49DX6shxAxY");
		$iframe->setAttribute('frameborder', '0');
		$iframe->setAttribute('allowfullscreen', true);
		$content->appendChild($iframe);
	//Appended frame element to content container
	
	//Fragment for storing questions and answers
	//Inside the array $json, there is a set of data labeled with key: questions and answers
	//These are the steps taken to produce the dl, dt, and dd tags in html with php
		$dl= $dom->createElement('dl');
		$frag= $dom->createDocumentFragment();
	
		foreach($json as $aboutMeSet){
			$dt=$dom->createElement('dt');
			$dt->appendChild($dom->createTextNode($aboutMeSet["question"]));
			$frag->appendChild($dt);
			
			foreach($aboutMeSet["answer"] as $paragraph){
				$dd=$dom->createElement('dd');
				$dd->appendChild($dom->createTextNode($paragraph));
				$frag->appendChild($dd);
			}
		}
		$dl->appendChild($frag);
		
	//Definition list is appended to the content container
	// $content is already added to the page before in template, and thus no need to add it again.
		$content->appendChild($dl);
	
	//Contact information for twitter
	$a= $dom->createElement('a');
	$a->setAttribute('href', "https://twitter.com/curiousBusyBee");
	$a->appendChild($dom->createTextNode("Find me on Twitter!"));
	$content->appendChild($a);
	
	
//full page is appended to the dom, thus ending DOM editing
	echo $dom->saveHTML();
	
//Adding javascript to page
	echo "<script type='text/javascript' src='script/style.js'></script>";
?>

