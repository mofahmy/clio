<?php

function print_checkboxes() {
  //echo "<form method=\"get\" action=\"index.php\">";

   $db = new PDO('sqlite:scripts/cfps.db');
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   $select = 'SELECT * FROM category';
   $query = $db->prepare($select);
   $query->execute();

   $col1 = array();
   $col2 = array();
   $col3 = array();
   $col4 = array();

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
   
   echo "</ul>\n</div>\n";
   echo "<div id=\"clearer\" class=\"clearer\"></div><br><br>\n";
   echo "<center><button onclick=\"redirect()\" class=\"getcfp-button\">Get CFPs</button></center>";

}

include("header.php");
print_checkboxes();
include("footer.php");

?>