<?php

function print_html_begin() {
  echo <<<HTML

<!doctype html>
<html lang="en">
<head>
    <title>CFP Harvester Index</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script src="js/accordion.js"></script>
</head>

<body>
    <center><a href="../clio"><img border="0" align="center" src="header.jpg"></a></center>
    <div>
    <center>
      <p>Click on a category name to view CFPs in that category.</p>
    </center>
    </div>

    
    
    <div class="wrapper">
    <div id="accordion">
HTML;
}

function print_html_end() {
  echo <<<HTML

    </div>
    </div>
    <br><br><br>
    <center>
    <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/3.0/88x31.png" /></a><br />
    <!--This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution-ShareAlike 3.0 Unported License</a>.<br>-->
    Data adapted from <a href="http://www.wikicfp.com/">WikiCFP</a>
    </center>

   </body>
  </html>
HTML;
}

function print_cfp($name, $full_name, $date, $location, $wikicfp_link, $official_link, $abstract_due, $submission_due, $notification_due, $final_due) {
  echo "<p>";

  echo"<b>$name</b>";
  if (strlen($full_name) > strlen($name)) {
    echo " : $full_name";
  }
  echo "<br>\n";

  if ($location != "N/A") {
    echo "<i>$location</i>";
    if ($date != "N/A") {
      echo " $date";
    }
    echo "<br>\n";
  }

  if ($official_link != "N/A") {
    echo "[<a href=\"$official_link\" target=\"_blank\">Website</a>] ";
  }
  echo "[<a href=\"$wikicfp_link\" target=\"_blank\">CFP</a>]";

  if ($submission_due != "N/A") {
    echo "<table>\n";
    echo "<col width='150'><col width='300'>\n";

    if ($abstract_due != "N/A") {
      echo "<tr><td>Abstract</td><td>$abstract_due</td></tr>\n";
    }

    echo "<tr><td>Submission</td><td>$submission_due</td></tr>\n";

    if ($notification_due != "N/A") {
      echo "<tr><td>Notification</td><td>$notification_due</td></tr>\n";
    }

    if ($final_due != "N/A") {
      echo "<tr><td>Final Version</td><td>$final_due</td></tr>\n";
    }
    echo "</table>\n";
  }

  echo "</p><br>\n";
}

function print_category_cfps($category_id) {
  $db = new PDO('sqlite:cfps.db');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $select = 'SELECT * FROM event WHERE category_id = :category_id';
  $query = $db->prepare($select);
  $query->bindParam(':category_id',$category_id);

  echo "<div><p>\n";
  $query->execute();

  while($results = $query->fetch()) {
    $name = $results['name'];
    $full_name = $results['full_name'];
    $date = $results['date'];
    $location = $results['location'];
    
    $wikicfp_link = $results['wikicfp_link'];
    $official_link = $results['official_link'];

    $abstract_due = $results['abstract_due'];
    $submission_due = $results['submission_due'];
    $notification_due = $results['notification_due'];
    $final_due = $results['final_due'];

    print_cfp($name, $full_name, $date, $location, $wikicfp_link, $official_link, $abstract_due, $submission_due, $notification_due, $final_due);
  }

  echo "</p></div>\n";
}

function print_category($category_id) {
  $db = new PDO('sqlite:cfps.db');
  $db->setAttribute(PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION);

  $select = 'SELECT count(id) FROM event WHERE category_id = :category_id';
  $query = $db->prepare($select);
  $query->bindParam(':category_id',$category_id);

  $query->execute();
  $count = $query->fetchColumn();

  $category = get_category_name($category_id);
  echo "<h3>".ucwords($category)." (".$count.")</h3>";

  if ($count > 0) {
    print_category_cfps($category_id);
  } else {
    echo "<div>\n<p>No CFPs at this time.</p>\n</div>\n";
  }
}

function get_category_name($category_id) {
  $db = new PDO('sqlite:cfps.db');
  $db->setAttribute(PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION);

  $select = 'SELECT name FROM category WHERE id = :id';
  $query = $db->prepare($select);
  $query->bindParam(':id',$category_id);

  $query->execute();
  $name = $query->fetchColumn();
  
  return $name;
}

function get_category_ids() {
  if (isset($_GET["category_ids"])) {
    //$array = $_GET["category_ids"];
    //rsort($array);
    //return explode(',',$array);
    //return explode(',',$_GET["category_ids"]);
    $array = explode(',',$_GET["category_ids"]);
    sort($array);
    return $array;
  }
  
  else return array(1,2,3,7,11,13,15,17,19,20,21,22,24,25,26,28,32,37,39);
}

print_html_begin();

$category_ids = get_category_ids();
foreach($category_ids as $id) {
  print_category($id);
}

print_html_end();

?>