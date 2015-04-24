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
    
    <center><a href="../clio"><img border="0" align="center" src="images/header.jpg"></a></center>
    <div>
      <center>
        <!--<h4>Welcome to Clio, a Call for Papers (CFP) harvester and indexer, updated daily. To get started:</h4>
                                                 <p>(1) Check your areas of interest - (2) Click the 'Get CFPs' button - (3) Bookmark the resulting page</p>-->
      </center>
    </div><br>
    <div id="wrapper">