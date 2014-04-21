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

// UserCake authentication
require_once("./models/config.php");

if (!securePage($_SERVER['PHP_SELF'])){
  // Forward to 404 page
  addAlert("danger", "Whoops, looks like you don't have permission to view that page.");
  header("Location: 404.php");
  die();
}

setReferralPage($_SERVER['PHP_SELF']);

// Admin page

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <title>UserFrosting Admin - Admin Dashboard</title>

    <?php require_once("includes.php");  ?>

    <!-- Page-specific CSS -->
    <link rel="stylesheet" href="css/typeahead.css" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap-switch.min.css" type="text/css" />
    
    <!-- Page Specific Plugins -->
	<script src="js/jquery.tablesorter.js"></script>
	<script src="js/tables.js"></script>
    <script src="js/bootstrap-switch.min.js"></script>
 
  </head>

  <body>

    <div id="wrapper">

      <!-- Sidebar -->
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      </nav>

      <div id="page-wrapper">
        <div class="row">
          <div id='display-alerts' class="col-lg-12">

          </div>
        </div>
        

      </div><!-- /#page-wrapper -->

    </div><!-- /#wrapper -->
    
    <script src="js/raphael/2.1.0/raphael-min.js"></script>
    <script src="js/morris/morris-0.4.3.js"></script>
    <script src="js/morris/chart-data-morris.js"></script>
    <script>
        $(document).ready(function() {
          // Get id of the logged in user to determine how to render this page.
          var user = loadCurrentUser();
          var user_id = user['id'];
          
          // Load the header
          $('.navbar').load('header.php', function() {
            $('#user_logged_in_name').html('<i class="fa fa-user"></i> ' + user['user_name'] + ' <b class="caret"></b>');
            $('.navitem-dashboard-admin').addClass('active');
          });

          alertWidget('display-alerts');
          
          // Initialize the transactions tablesorter
          $('#transactions .table').tablesorter({
              debug: false
          });
          
        });      
    </script>
  </body>
</html>
