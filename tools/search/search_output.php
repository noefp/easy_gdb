<?php include_once realpath("../../header.php");?>

<div class="page_container">


<?php
// Connecting, selecting database
include_once realpath ("$conf_path/database_access.php");

if(!getConnectionString()==null)
{
  $dbconn = pg_connect(getConnectionString())
    or die('Could not connect: ' . pg_last_error());


$raw_input = $_GET["search_keywords"];

$quoted_search = 0;

if ( preg_match('/^".+"$/',$raw_input ) ) {
  $quoted_search = 1;
  // echo "<p>RAW $raw_input</p>";
}

function test_input($data) {
  
  $data = preg_replace('/[\<\>\t\;]+/',' ',$data);
  $data = htmlspecialchars($data);
  
  

  if ( preg_match('/\s+/',$data) ) {
    $data_array = explode(' ',$data,99);

    foreach ($data_array as $key=>&$value) {
        if (strlen($value) < 3) {
            unset($data_array[$key]);
        }
    }

    $data = implode(' ',$data_array);
  }

  $data = stripslashes($data);

  return $data;
}

$search_input = test_input($raw_input);
// $max_row = 25;


echo "\n<br><h3>Search Input</h3>\n<div class=\"card bg-light\"><div class=\"card-body\">$search_input</div></div><br>\n";

?>
 
<?php include_once realpath("search_annot.php");?>


<?php
// Closing connection
pg_close($dbconn);
?>
<?php
}
else
{
  echo "<div class=\"alert alert-danger margin-20\" style=\"text-align:center\">";
  echo "<strong>Info: </strong><i> DataBase</i> not available";
  echo"</div>";
}
?>

<br>
<br>
</div>

<script type="text/javascript">
  // $(".tblAnnotations").dataTable({
  //   dom:'Bfrtip',
  //   buttons:[{
  //     extend:'csv',
  //     text:'Download',
  //     title:"egdb_annotations",
  //     fieldBoundary: '',
  //     fieldSeparator:"\t"},
  //     {
  //       extend:'excel',
  //       text:'Excel',
  //       title:"egdb_annotations",
  //       fieldSeparator:"\t"
  //     },
  //     'copy'],
  //     bFilter:false
  //   });
    
    $("#tblAnnotations").dataTable({
    	dom:'Bfrtlpi',
      "oLanguage": {
         "sSearch": "Filter by:"
       },
      buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print', 'colvis'
      ]
    });
  
    $("#tblResults_filter").addClass("float-right");
    $("#tblResults_info").addClass("float-left");
    $("#tblResults_paginate").addClass("float-right");

</script>


<?php include_once realpath("$easy_gdb_path/footer.php");?>
