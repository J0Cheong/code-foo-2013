<?php
//3. Write a program to find the given words from the included word search. Both word search and words can be found at "word-search.txt"

//template includes logo, and navigation menu which will appear on every page
/*
Note: using template.php
	the DOMDocument prototype has already been instanced to a global variable named $dom
	the use of $content will be to place information pertaining to the current page 
	the $title element has been declared and applied to the head but a name is still needed
	
*/ 
	include 'JSON/template.php';
//CHANGE THE TITLE TO THE NAME OF THE CURRENT PAGE
	$title->appendChild($dom->createTextNode("Wordsearch Puzzle"));

//Adding title for content:
	$h2= $dom->createElement("h2");
	$h2->nodeValue="Find the hidden words";
	$content->appendChild($h2);
//Open file containing wordsearch puzzle
	$txtData = file('material/word-search.txt');
	$textFile = array();
	
// Content returns the text file as an array for each line in the document, the next function will change every letter to lowercase for the rest of the program.
	foreach ($txtData as $line){
		array_push($textFile, strtolower($line));
	}
	
// Need to split into word list and words before exploding the crossword for convenience
	$marker = "words to find:";
	$textFile = array_filter(array_map('trim', $textFile));
	$textFile = preg_replace('/\s+/', ' ', $textFile);	
	$key = array_search($marker, $textFile);

// Splitting two halfs of the puzzle between the marker
	$puzzle = array_slice($textFile, 0, $key-1);
	$wordList = array_slice($textFile, $key-1);
	
	foreach(array($puzzle,$wordList) as $data){
		$data= implode("\n", $data);
		$pre= $dom->createElement("pre");
		$pre->appendChild($dom->createTextNode($data));
		$content->appendChild($pre);
	}
	
// Link to next page
	$form= $dom->createElement("form");
	$form->setAttribute("action", "wordSearchComplete.php");
	$form->setAttribute("method", "POST");
	
	$button= $dom->createElement("input");
	$button->setAttribute("type", "submit");
	$button->setAttribute("value", "Show Answer");
	$form->appendChild($button);
	
	
	$content->appendChild($form);
	
// Contact information for twitter
	$a= $dom->createElement('a');
	$a->setAttribute('href', "https://twitter.com/curiousBusyBee");
	$a->appendChild($dom->createTextNode("Find me on Twitter!"));
	$content->appendChild($a);
	
// Full page is appended to the dom, thus ending DOM editing
	echo $dom->saveHTML();
	
// Adding javascript to page
	echo "<script type='text/javascript' src='script/style.js'></script>";
?>
