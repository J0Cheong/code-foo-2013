<?php
//JSON: all content is placed within the JSON directory, mainmenu contains a template of the title as navigation for the site. Using file_get_contents will retrieve json formatted files for manipulation within php
$file = file_get_contents('mainMenu.json', FILE_USE_INCLUDE_PATH);
//for safety reasons, the file is transformed into utf8 format to use with php 
$str= utf8_encode($file);
$json= json_decode($str, true);

$dom = new DOMDocument();
//Instantiate a new document
$html = $dom->createElement('html');


//Creation of head element
$head = $dom->createElement('head');
$title= $dom->createElement('title');
$head->appendChild($title);
$html->appendChild($head);
// Head node is attached to document

// Creation of body content
$body = $dom->createElement('body');

	//Creation of logo for page
		$h1= $dom->createElement('h1');
		$h1->appendChild($dom->createTextNode($json["logo"]));
		$body->appendChild($h1);
	//Appended logo to page
	
	//Creation of navigation bar
		$nav= $dom->createElement('div');
		$nav->setAttribute('id', 'nav');
		$ul= $dom->createElement('ul');
		//fragment will be used to store all the menus from navigation until needed
		
		$frag= $dom->createDocumentFragment();
		//a $link is created as an array filled with the title and the corresponding link 
		//Use $link[0] to retrieve title
		//Use $link[1] to retrieve actual link to traverse pages
		foreach($json["navigation"] as $link){
			$aNav= $dom->createElement('a');
			$aNav->appendChild($dom->createTextNode($link['title']));
			$aNav->setAttribute("href", $link['link']);
			$liNav= $dom->createElement('li');
			$liNav->appendChild($aNav);
			$frag->appendChild($liNav);
		}
		//Navigation menu appended in div box with id: "nav"
		$nav->appendChild($frag);
		$body->appendChild($nav);
	//Appended Navigation Menu to page
	
	//Creation of divider for page content
		$content= $dom->createElement('div');
		$content->setAttribute("id", "content");
		$body->appendChild($content);
	//Declared container with ID: "content"
	
	
$html->appendChild($body);
//Body Node is attached to document 

$dom->appendChild($html);
//Document is attached to the Document Object Model
?>