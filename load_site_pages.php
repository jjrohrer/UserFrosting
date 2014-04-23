<?php
/*

UserFrosting Version: 0.1
By Alex Weissman
Copyright (c) 2014

Based on the UserCake user management system, v2.0.2.
Copyright (c) 2009-2012

UserFrosting, like UserCake, is 100% free and open-source.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

// Request method: GET

require_once("models/config.php");

set_error_handler('logAllErrors');

try {
  // Load list of site pages.  Recommended access level: admin only
  if (!securePage($_SERVER['PHP_SELF'])){
	addAlert("danger", "Whoops, looks like you don't have permission to load site pages.");
	echo json_encode(array("errors" => 1, "successes" => 0));
	exit();
  }
  
  $dirPages = getPageFiles(); //Retrieve list of pages in root usercake folder
  $dbpages = fetchAllPages(); //Retrieve list of pages in pages table
  $creations = array();
  $deletions = array();
  $originals = array();
  
  //Check if any pages exist which are not in DB
  foreach ($dirPages as $page){
	  if(!isset($dbpages[$page])){
		  $creations[] = $page;	
	  }
  }
  
  //Enter new pages in DB if found
  if (count($creations) > 0) {
	  createPages($creations)	;
  }
  
  // Find pages in table which no longer exist
  if (count($dbpages) > 0){
	  //Check if DB contains pages that don't exist
	  foreach ($dbpages as $dbPageName=>$asrDbPage){
          if (!in_array($dbPageName,$dirPages)) {
		  //if(!isset($pages[$page['page']])){
			$deletions[] = $asrDbPage['id'];
              print "<br> $dbPageName wasn't there - so deleting";

		  } else {
			$originals[] = $asrDbPage['id'];
		  }
	  }
  }
//    print "<br>".__FILE__.__LINE__."<br>dirPages: <pre>";
//    print_r( $dirPages );
//    print "</pre>";
//
//    print "<br>".__FILE__.__LINE__."<br>dbpages: <pre>";
//    print_r( $dbpages );
//    print "</pre>";
//
//    print "<br>".__FILE__.__LINE__."<br>new: <pre>";
//    print_r( $creations );
//    print "</pre>";
//
//    print "<br>".__FILE__.__LINE__."<br>to delte: <pre>";
//    print_r( $deletions );
//    print "</pre>";
//
//    print "<br>".__FILE__.__LINE__."<br>db: <pre>";
//    print_r( $originals );
//    print "</pre>";

    $allPages = fetchAllPages();
  // Merge the newly created pages, plus the pages slated for deletion, load their permissions, and set a flag (C)reated, (U)pdated, (D)eleted
  foreach ($allPages as $page){
	$id = $page['id'];
	$name = $page['page'];
	if (in_array($name, $creations)){
	  $allPages[$name]['status'] = 'C';
	} else if (in_array($id, $deletions)){
	  $allPages[$name]['status'] = 'D';
	} else {
	  $allPages[$name]['status'] = 'U';
	}
	$pagePermissions = fetchPagePermissions($id);
	if ($pagePermissions)
	  $allPages[$name]['permissions'] = $pagePermissions;
	else
	  $allPages[$name]['permissions'] = array();
  }


    //Delete pages from DB
  if (count($deletions) > 0) {
	  deletePages($deletions);
  }

} catch (PDOException $e) {
  addAlert("danger", "Oops, looks like our database encountered an error.");
  error_log($e->getMessage());
} catch (ErrorException $e) {
  addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
} catch (RuntimeException $e) {
  addAlert("danger", "Oops, looks like our server might have goofed.  If you're an admin, please check the PHP error logs.");
  error_log($e->getMessage());
}

restore_error_handler();

echo json_encode($allPages);
?>
