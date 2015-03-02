<?php

function print_html_begin() {
  echo <<<HTML

<!doctype html>
<html lang="en">
<head>
    <title>CFP Harvester Index</title>
    <meta charset="utf-8" />
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="pragma" content="no-cache">
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    
    <script language="Javascript">
    $(document).ready(function(e) {
        if ($("#refresh").val() == 'yes') { location.reload; } else { $('#refresh').val('yes'); }
      });
    </script>


    <script language="Javascript">
        function sortNumber(a,b) {
    return a - b;
  }
    </script>
    
    <script language="Javascript">
    function redirect(){
    var selected = new Array();
    $('#checkboxes input:checked').each(function() {
        selected.push($(this).attr('value'));
      });
    
    category_ids = selected.join();
    
    window.location = "list.php?category_ids=".concat(category_ids);
    }
    </script>

</head>

<body>
    
    <center><a href="../clio"><img border="0" align="center" src="header.jpg"></a></center>
    <div>
      <center>
        <!--<h4>Welcome to Clio, a Call for Papers (CFP) harvester and indexer, updated daily. To get started:</h4>
                                                 <p>(1) Check your areas of interest - (2) Click the 'Get CFPs' button - (3) Bookmark the resulting page</p>-->
      </center>
    </div><br>
    <div id="wrapper">
HTML;
}

function print_html_end() {
  echo <<<HTML
    </div>
   </body>
    </html>
HTML;
}

function print_checkboxes() {
  //echo "<form method=\"get\" action=\"index.php\">";

   $db = new PDO('sqlite:cfps.db');
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   $select = 'SELECT * FROM category';
   $query = $db->prepare($select);
   $query->execute();

   $col1 = array();
   $col2 = array();
   $col3 = array();
   $col4 = array();

   //echo "<div id=\"checkboxes\" style=\" background:#fafafa; color:#222; padding:10px;\">\n";
   echo "<div id=\"checkboxes\">\n";
   echo "<ul class=\"checkbox-grid\">\n";
   while($results = $query->fetch()) {
     $id = $results['id'];
     $name = ucwords($results['name']);

     $item = "<li><input type=\"checkbox\" name=\"$name\" id=\"$name\" class=\"css-checkbox\" value=\"$id\"/><label for=\"$name\" class=\"css-label\">$name</label></li>";

     
     if ($id > 44 * 3 / 4) { 
       array_push($col4, $item); 
     } else if ($id > 44 * 2 / 4) {
       array_push($col3, $item);
     } else if ($id > 44 * 1/4) {
       array_push($col2, $item);
     } else {
       array_push($col1, $item);
     }

      
    }
   
   $max = max(sizeof($col1), sizeof($col2), sizeof($col3), sizeof($col4));
   for ($i = 0; $i < $max; $i++) {
    
     if (isset($col1[$i])) { echo "$col1[$i]\n"; }    
     if (isset($col2[$i])) { echo "$col2[$i]\n"; }
     if (isset($col3[$i])) { echo "$col3[$i]\n"; }
     if (isset($col4[$i])) { echo "$col4[$i]\n"; }
   }
   
   //foreach ($col1 as $item) { echo "$item\n"; }
   //foreach ($col2 as $item) { echo "$item\n"; }
   //foreach ($col3 as $item) { echo "$item\n"; }
   //foreach ($col4 as $item) { echo "$item\n"; }
   
   echo "</ul>\n</div>\n";
   echo "<div id=\"clearer\" class=\"clearer\"></div><br><br>\n";
   echo "<center><button onclick=\"redirect()\" class=\"getcfp-button\">Get CFPs</button></center>";

}

print_html_begin();
print_checkboxes();
print_html_end();

?>