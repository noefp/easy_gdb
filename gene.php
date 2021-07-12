<?php include_once 'header.php';?>
<?php include_once 'tools/common_functions.php';?>

<div class="page_container">
<br>
<div class="card bg-light">
  <?php echo "<div id=\"query_gene\" class=\"card-body\">".test_input($_GET["name"])."</div>" ?>
</div>
<?php

$current_version = $max_version;

// Connecting, selecting database
include_once realpath ("$conf_path/database_access.php");
$dbconn = pg_connect(getConnectionString())
    or die('Could not connect: ' . pg_last_error());

$gene_name = test_input($_GET["name"]);
// $search_input = $gene_name;
// $gene_name_displayed = $gene_name;

// echo "\n\n<br><br><h1>GENE NAME: $gene_name</h1><br><br>\n\n";


// Performing SQL query

$query = "SELECT * FROM gene FULL OUTER JOIN annotation_version USING(annotation_version_id) FULL OUTER JOIN species USING(species_id) WHERE gene_name='".pg_escape_string($gene_name)."'";
// $query = "SELECT * FROM gene WHERE gene_name='".pg_escape_string($gene_name)."'";


// $query = "SELECT gene_id,gene_version FROM gene join gene_version ON (gene.gene_version_id=gene_version.gene_version_id) WHERE gene_name='".pg_escape_string($gene_name)."'";
$res = pg_query($query) or die("The gene $gene_name was not found in the database. Please, check the spelling carefully or try to find it in the search tool.");
// $res = pg_query($query) or die('Query failed: ' . pg_last_error());
$gene_row = pg_fetch_array($res,0,PGSQL_ASSOC);
$gene_id = $gene_row["gene_id"];
$species_id= $gene_row["species_id"];

$species_name = $gene_row["species_name"];
$annot_version = $gene_row["annotation_version"];


  include_once 'jb_frame.php';
  include_once 'annot_desc.php';
  include_once 'gene_seq.php';

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
  var query_gene = "<?php echo $gene_name ?>";
  var sps_name = "<?php echo $species_name ?>";
  var annot_v = "<?php echo $annot_version ?>";
  document.getElementById('query_gene').innerHTML = query_gene+" &nbsp; <i>"+sps_name+"</i> &nbsp; v"+annot_v;
  
  // document.getElementById('query_gene').innerHTML = query_gene;
  // document.getElementById('query_gene').style.display = "block";

</script>
