<?php include_once 'header.php';?>

<div class="page_container">
<br>
<div class="card bg-light">
  <div id="query_gene" class="card-body" style="display:none">
  </div>
</div>
<?php

$current_version = $max_version;

// Connecting, selecting database
include_once realpath ("$conf_path/database_access.php");
$dbconn = pg_connect(getConnectionString())
    or die('Could not connect: ' . pg_last_error());

$gene_name = test_input($_GET["name"]);
$search_input = $gene_name;
$gene_name_displayed = $gene_name;

// echo "\n\n<br><br><h1>GENE NAME: $gene_name</h1><br><br>\n\n";

function test_input($data) {
  $data = stripslashes($data);
  $data = preg_replace('/[\<\>]+/','',$data);
  $data = htmlspecialchars($data);

  // if (! preg_match('/\.\d$/',$data) ) {
  //   $data = preg_replace('/$/','.1',$data);
  // }

  return $data;
}


// Performing SQL query
$query = "SELECT gene_id,genome_version FROM gene WHERE gene_name='".pg_escape_string($gene_name)."'";
$res = pg_query($query) or die("The gene $gene_name was not found in the database. Most probably this gene was not associated to a gene from the current version.");
// $res = pg_query($query) or die('Query failed: ' . pg_last_error());
$gene_row = pg_fetch_array($res,0,PGSQL_ASSOC);
$ori_gene_version = $gene_row["genome_version"];
$gene_version = $ori_gene_version;
$gene_id = $gene_row["gene_id"];

// echo "\n\n<br><br><h1>GENE NAME: $gene_id $gene_version</h1><br><br>\n\n";


if ($ori_gene_version != $current_version) {
  // $query2 = "SELECT gene_id1 FROM gene_gene WHERE gene_id2=$tmp_gene_id";
  $query2 = "SELECT * FROM gene JOIN gene_gene ON(gene_id=gene_id1) WHERE gene_id2='".pg_escape_string($gene_id)."';";

  $res2 = pg_query($query2) or die("<h4 class=\"well\" style=\"line-height:24px;\">The gene $gene_name was not found in the database. Most probably this gene was not associated to a gene from the current version.</h4>");
  // $res2 = pg_query($query2) or die('Query failed2: ' . pg_last_error());
  // $gene_gene_row = pg_fetch_array($res2,0,PGSQL_ASSOC);

  echo "<p> Annotation is only available for the most recent gene version (v$current_version). Please, click on the Current Gene link below to access the annotations from the most similar gene.</p><br>\n";
  echo "<table class=\"table annot_table\">\n<tr><th>Current Gene</th><th>Gene Found</th><th>Gene Version</th></tr>\n";

  while ($gene_gene_row = pg_fetch_array($res2, null, PGSQL_ASSOC)) {

  // $gene_id = pg_fetch_result($res2,0,0);

    $gene_id = $gene_gene_row["gene_id1"];
    $new_gene_name = $gene_gene_row["gene_name"];

  echo "<tr><td><a href=\"gene.php?name=$new_gene_name\" target=\"_blank\">$new_gene_name</a></td><td>$gene_name</td><td>$gene_version</td></tr>\n";
  // echo "<tr><td><a href=\"pp_annot.php?name=$new_gene_name\" target=\"_blank\">$new_gene_name</a></td><td><a href=\"pp_annot.php?name=$gene_name\" target=\"_blank\">$gene_name</a></td><td>$gene_version</td></tr>\n";
  }
  pg_free_result($res2);

  echo "</table>\n\n";

  // echo "\n<br>\n";
  // include_once 'pp_annot_desc.php';
  // echo "\n\n<br><br><h1>GENE NAME2: $gene_id</h1><br><br>\n\n";
}
else {
  // echo "\n<br>\n";
  // include_once 'ppdb_search_input_form.php';
  // include_once 'pp_annot_desc.php';
  include_once 'annot_desc.php';
  include_once 'other_gene_ids.php';
  include_once 'gene_seq.php';
}


  // echo "\n<br>\n";
  // include_once 'annot_desc.php';
  // include_once 'other_gene_ids.php';
  // include_once 'gene_seq.php';


?>


<?php
// Free resultset
// pg_free_result($res);

// Closing connection
pg_close($dbconn);
?>

<br>
<br>
</div>


<?php include_once 'footer.php';?>

<script>

  var query_gene = "<?php echo $gene_name ?>"
  document.getElementById('query_gene').innerHTML = query_gene;
  document.getElementById('query_gene').style.display = "block";

</script>
