<?php
require 'models.php';
/**
* Loop xml files in a given folder and put their content in a table
*
*/
function loopFiles() {
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('books'));
	$books = array();
	foreach ($iterator as $file) {
		// loop only xml files
	    if ($file->getExtension() == 'xml') {
	    	//put the xml content in json decoded object
	      $array = json_decode(json_encode((array)simplexml_load_string(file_get_contents($file))),true);
			  array_push($books, $array['book']);
	    }
	}
	// save the books to the database
	$database = new Database();
	$database->save($books);
}
loopFiles();
?>