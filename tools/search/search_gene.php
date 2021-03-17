<div class="colapse_section pointer_cursor" data-toggle="collapse" data-target="#gene_section"><h3>Genes found</h3></div>

<div id="gene_section" class="collapse in">
  <br>
<div class="data_table_frame">
<?php
// $current_version = "3.3";
// Performing SQL query
$query = "SELECT * FROM gene WHERE gene_name ILIKE '%".pg_escape_string($search_input)."%' ORDER BY genome_version DESC, gene_name ASC";
$res = pg_query($query) or die('Query failed: ' . pg_last_error());

if (pg_fetch_assoc($res)) {
  $res = pg_query($query) or die('Query failed: ' . pg_last_error());

  // Printing results in HTML
  echo "<table class=\"table annot_table tblAnnotations\">\n<thead><tr><th>Gene Found</th><th>Gene Version</th></tr></thead>\n<tbody>\n";
  // $counter = 0;

  while ($line = pg_fetch_array($res, null, PGSQL_ASSOC)) {

      $found_gene_name = $line["gene_name"];
      $found_gene_id = $line["gene_id"];
      $found_gene_version = $line["genome_version"];

      echo "<tr><td><a href=\"/db/gene.php?name=$found_gene_name\" target=\"_blank\">$found_gene_name</a></td><td>$found_gene_version</td></tr>\n";
      // $counter++;
      // if ($counter >= $max_row) {
        // echo "<tr><td colspan=\"4\">Number of genes found exceeded the limit to display, Please refine your search.</td></tr>\n";
        // break;
      // }

  }
  echo "</tbody>\n</table>\n";
}
else {
  echo "<p>No genes found.</p>\n";
}
// Free resultset
pg_free_result($res);
?>

</div>
</div>
