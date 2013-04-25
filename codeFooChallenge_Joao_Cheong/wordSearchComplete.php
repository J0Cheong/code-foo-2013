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
//This page no longer needs the $content divider due to some markup limitations, a new content divider will be set up later 
	$title->appendChild($dom->createTextNode("Wordsearch Puzzle Answers"));
	$body->removeChild($content);
	echo $dom->saveHTML();
	
/* ----------------------------------------------------

	I like to start with pencil and paper before I write any code to give myself a guide in solving the problem. The proceeding explanation is an overview of how I wanted to approach the problem.

I. Analysis: 

	- "word-search.txt" file contains both the puzzle and the words which can be found within the puzzle. 
	- Each part is separated by an empty line and the phrase "Words to find"
	- Vocabulary have uppercase first letter but all characters in puzzle are lower case

II. Projected Goal:
	-Give some visual indication of the words found in the word search puzzle
	
III. Approach: 

	1. Split the content of the txt into two separate entities: one for the puzzle and another for the words
	2. Perform one search horizontally, vertically, and diagonally. First try horizontal
	3. Duplicate the words and reverse the spelling
	4. Join all words to search for and use a regular expression method to search for the words in the puzzle
	5. Notify all found words during search
	6. Mark found words in the puzzle (bold, change color, etc.)
	7. Remove found words and their reverse counterparts from the search list
	8. Change orientation of puzzle
	9. Repeat search for other directions
	10. Display words not found in the puzzle
	
IV. Commentary:
	I tried to avoid using the brute force method of searching through the puzzle (m*n) number of times because it is inefficient and quite expensive. My method might require me to write more code than normal, but would have greater scalability if the multi-dimension puzzle is to grow in size. 
	
	Due to how PHP processes text files and returns them as strings, I decide to use Regular expressions to my advantage to decrease the number of searches done. Seeing how effective this worked, I decided to replicate the same format for the vertically oriented puzzle. The loop only ran for as many times as there were rows and columns. (45 times) For every word found in the puzzle, I remove them from the list, which will be beneficial for the program during the diagonal search.
	
	During the diagonal searches, the probability that the current indexed letter matches the first or last letter for any of the words will have greatly diminished, thanks to the first two searches performed. I put a few extra conditions in the search to diminish the number of false-positive searches the program had to perform. Finally, in order to prevent the program from breaking, should the words list become empty, all tests will return quickly without searching intensively in the puzzle as no further calculations need to be performed.

? Note		
			
This application is designed to be friendly to changes from within the PHP file. To search for other puzzles, a change should be made on line 42 & 50:
	LINE 55: $fileName: "example.txt"  /ENTER YOUR FILE NAME
	LINE 64: $marker: "example marker" /ENTER A SIGNIFICANT POINT TO SPLIT THE PUZZLE
 ----------------------END--------------------------- */
	
	$txtData = file('material/word-search.txt');
	$textFile = array();
	
	// Content returns the text file as an array for each line in the document, the next function will change every letter to lowercase for the rest of the program.
	foreach ($txtData as $line){
		array_push($textFile, strtolower($line));
	}
	
	// Need to split into word list and words before exploding the crossword for convenience
	$marker = "wordstofind:";
	$textFile = array_filter(array_map('trim', $textFile));
	$textFile = preg_replace('/\s+/', '', $textFile);	
	$key = array_search($marker, $textFile);

	// splitting two halfs of the puzzle between the marker
	$puzzle = array_slice($textFile, 0, $key-1);
	$wordList = array_slice($textFile, $key);
	$printList= $wordList; //creation of word list for final printing
	
	/*-----------------PUZZLE AND WORDS HAVE BEEN SPLIT------------------- */
	
	//Initialization of the rows and columns to keep the position of the found words
	$rowFound = array(); 
	$colFound = array();
	
	//length of the row and columns in the normal orientation
	$rows = count($puzzle); 
	$columns = strlen($puzzle[0]);	
	
	//creation of pattern to search for in the regular expression. This function will group all the words from the words list together and mirror the pattern incase the words are spelled backwards.
	foreach($wordList as $word){
		array_push($wordList, strrev($word));
	}
	$pattern= implode("|", $wordList);

	// creation of the vertically flipped wordSearch
	$vPuzzle = array();
	for ($j=0; $j<$columns; $j++){
			$string= ''; //strings are created and pushed into an array to keep the format similar to the orginal puzzle, making the search function easier to write
		for ($i=0; $i<$rows; $i++){
			$string .= $puzzle[$i][$j]; //string concatenation in a loop	
		}
		array_push($vPuzzle, $string);
	}
	
	//the puzzle is put inside another array to perform a foreach loop which will pass each puzzle into the solver consecutively to find matching words
	foreach(array($puzzle, $vPuzzle) as $oPuzzle){
		for($i=0; $i<count($oPuzzle); $i++){
			
			//first check to see if the word list is empty, if it is not, it will check for a matching pattern if there is one or more the next set of codes will run
			if( preg_match_all("/".$pattern."/",$oPuzzle[$i],$matches, PREG_OFFSET_CAPTURE)){ 
				for($j=0;$j<count($matches[0]);$j++){ //a loop for all the matching patterns found
					$word = $matches[0][$j][0]; //the first index, [0] refers to the word found during this search
					$pos = $matches[0][$j][1]; //the second index, [1] is the offset at which the letter is found at
					foreach(array($word, strrev($word)) as $words){
						$indexLoc = array_search($words, $wordList); //locates the found word's index inside the word list 
						unset($wordList[$indexLoc]); //removes the found word from the list
					}
					
					for($k=0; $k<strlen($word); $k++){
						//the coordinate at where to locate the word is recorded in these two variables
						$rowFound[]=$i; 
						$colFound[]=$pos++;
					}
				}	
			}
		}
		
		//The next set of code will prepare the program for the vertical orientation, after the second test is finished, these variables will revert back to its original connotation 
			$magicHat = $rowFound; // magicHat is a place holder to allow the row pos to change over to the column position
			$rowFound = $colFound; // swaps the row position and the col position of found letters
			$colFound = $magicHat;
			unset($magicHat); //magicHat will not be used further on
	}
	
	/*-----------------SOLVING FOR WORDS IN DIAGONAL------------------- */
	// A bunch of words have been removed from the previous iterations, less computation is needed in the proceeding set of iterations even though it looks long.
	
	// Clean up the array indexing before working on it for diagonals
	$wordList = array_values($wordList); 
	
	for($i=0; $i<$rows; $i++){
		for($j=0; $j<$columns; $j++){
			for($k=0; $k<count($wordList); $k++){ 
			
				//even though k will not run when the word list is empty, it makes sense logically to check if the list is empty
				if(!empty($wordList) && $puzzle[$i][$j]===$wordList[$k][0]){ //checks to make sure the first letter matches
					$word = $wordList[$k]; //current word being searched
					$len = strlen($word);
					$cond1 = $i+$len < $rows; //conditional logic to prevent searching word words that go outside the possible dimension
					$cond2 = $j+$len < $columns; //$condition 2 and 3 are checks depending on which diagonal direction to check for
					$cond3 = $j-$len >= 0;
	
					foreach(array($cond2, $cond3) as $dirCond){
						switch($dirCond){
							case $cond2: //if $cond2 is true, try to search to the right
								$dir = 1;
								break;
							case $cond3: //if $cond3 is true, search towards the left, only one condition is true at a time because of the for each loop
								$dir = -1;
								break;
						}
						
						if($cond1 && $dirCond && $puzzle[$i+$len-1][$j+$dir*($len-1)]===$wordList[$k][$len-1]){	
							 //checks to make sure limit is not exceeded and last letter matches. If last letter matches, the probability the word is found is much higher
								$diagRowIndex= array(); //set up a diagonal array for storing letters
								$diagColIndex= array(); //keep these seperate from the real coordinate arrays to not push in coordinates which could be wrong
								
								for($l=0;$l<$len;$l++){
									if($puzzle[$i+$l][$j+$dir*$l]===$wordList[$k][$l]){
										$found= true;
										array_push($diagRowIndex, $i+$l); //slowest part of the program, matching puzzle, letter by letter
										array_push($diagColIndex, $j+$dir*$l);
									}else{
										$found=false; //if ever the search does not agree, test breaks and nothing is found
										break;
									}
								}
								
								//if a match is found, the recording coordinates will be appended to the real coordinate system
								if($found){
									$rowFound= array_merge($rowFound, $diagRowIndex);
									$colFound= array_merge($colFound, $diagColIndex);
									foreach(array($word, strrev($word)) as $words){ //removes the word found for faster searching in upcoming iterations
										$indexLoc= array_search($words, $wordList);
										unset($wordList[$indexLoc]);
									}
									$wordList= array_values($wordList); //to make sure the index is reorganized
									$found = false; //resets found value
								}
						} //check full word loop
					} //check direction loop
				} //check first letter loop
			} // k loop
		} //j loop
	} //i loop
	
	/*-----------------FINALIZING PUZZLE FOR PRODUCTION------------------- */
	//This for loop is responsible for spliting each string inside the puzzle into arrays such that style can be inserted in between the found letters. if it is not seperated into an array first the indexing will break
	for ($i=0; $i<count($puzzle); $i++){
		$puzzle[$i]= str_split($puzzle[$i]);
	}
	
	//prepend and append terms to add color and strength behind the selected letters. These styles will be used in the upcoming for loop
	$pre= "<span style='color: #00FF00;'><b>"; 
	$app= "</b></span>";
	for ($i=0; $i<count($rowFound); $i++){
		$y= $rowFound[$i]; //y for vertical axis and x for horizontal axis
		$x= $colFound[$i];
		$puzzle[$y][$x]= $pre.$puzzle[$y][$x].$app; 
	}

	//implode recombines the previously formed arrays back into strings with spacing
	for ($i=0; $i<count($puzzle); $i++){
		$puzzle[$i]= implode(' ',$puzzle[$i]);
	}
	
	//final implode to combine all arrays into one chunk of text seperated by new line
	$puzzle= implode("\n", $puzzle);
	
	$printList= implode("\n",$printList);
	
	//It has been mentioned in the php documentations that any markups added by using DOMDocument->createTextNode will not be erased and instead the html tags would appear on the page. This is undesirable, which is why saveHTML was called at the top to load up the initial template before generating the puzzle and wordSearch with loadHTML with one final $dom->saveHTML for the "return button" at the very end. This would not be wise for large pages as dom manipulation is very expensive. 
	$string= "<div id='content'><pre id='puzzle'>".$puzzle."</pre><pre id='words'>".$printList."</pre></div>";
	$content= $dom->loadHTML($string);
	//redefining the dom to add on element under answers
	
	//Adding button to allow user to return to previous page
	$form= $dom->createElement("form");
	$form->setAttribute("action", "wordSearch.php");
	$form->setAttribute("method", "POST");
	
	$button= $dom->createElement("input");
	$button->setAttribute("type", "submit");
	$button->setAttribute("value", "Return to puzzle");
	$form->appendChild($button);
	
	$dom->appendChild($form);

//full page is appended to the dom, thus ending DOM editing
	echo $dom->saveHTML();
	
//Adding javascript to page
	echo "<script type='text/javascript' src='script/style.js'></script>";
?>
