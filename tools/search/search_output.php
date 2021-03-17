<?php include_once realpath("../../header.php");?>

<div class="page_container">


<?php
// Connecting, selecting database
include_once realpath ("$conf_path/database_access.php");
$dbconn = pg_connect(getConnectionString())
    or die('Could not connect: ' . pg_last_error());

$search_input = test_input($_GET["search_keywords"]);
// $max_row = 25;

echo "\n<br><h3>Search Input</h3>\n<div class=\"card bg-light\"><div class=\"card-body\">$search_input</div></div><br>\n";

function test_input($data) {
  $data = preg_replace('/[\<\>]+/',' ',$data);
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
?>


<?php include_once realpath("search_annot.php");?>


<?php
// Closing connection
pg_close($dbconn);
?>

<br>
<br>
</div>

<script type="text/javascript">
  $(".tblAnnotations").dataTable({
    dom:'Bfrtip',
    buttons:[{
      extend:'csv',
      text:'Download',
      title:"AETAR_annotations",
      fieldBoundary: '',
      fieldSeparator:"\t"},
      {
        extend:'excel',
        text:'Excel',
        title:"AETAR_annotations",
        fieldSeparator:"\t"
      },
      'copy'],
      bFilter:false
    });
</script>


<?php include_once realpath("$easy_gdb_path/footer.php");?>
