
<!-- <h3>Other Gene Versions</h3> -->

<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#gene_ver_section" aria-expanded="true">
  Other Gene Versions
</div>

<div id="gene_ver_section" class="collapse in">
  <br>

<?php
// echo "\n\n<br><br><h1>GENE ID: $gene_id</h1><br><br>\n\n";

$gene_id_query = "SELECT * FROM gene JOIN gene_gene ON(gene_id=gene_id2) WHERE gene_id1='".pg_escape_string($gene_id)."' ORDER BY genome_version ASC";
// $gene_id_query = "SELECT * FROM gene JOIN gene_gene ON(gene_id=gene_id2) WHERE gene_id1='$gene_id' ORDER BY genome_version DESC, gene_name ASC";
$gid_res = pg_query($gene_id_query) or die('Query failed: ' . pg_last_error());

if ($gid_res) {
  // Printing results in HTML
  echo "<table class=\"table annot_table\">\n<tr><th>Gene Name</th><th>Version</th></tr>\n";

  while ($line = pg_fetch_array($gid_res, null, PGSQL_ASSOC)) {
      $old_gene_name = $line["gene_name"];
      $gene_version = $line["genome_version"];
      $version_class = str_replace(".","_",$gene_version);
      echo "<tr><td><a href=\"search_output.php?search_keywords=$old_gene_name\" target=\"_blank\">$old_gene_name</a></td><td>$gene_version</td></tr>\n";
      // echo "<tr class=\"v$version_class\"><td><a href=\"gene.php?name=$old_gene_name\" target=\"_blank\">$old_gene_name</a></td><td>$gene_version</td></tr>\n";
  }

  echo "</table>\n\n";

  // Free resultset
  pg_free_result($gid_res);
}

?>

<br>
</div>

<!-- <style>
  .v3_1 {
    background-color:#444
  }
  .v3_0 {
    background-color:#666
  }
  .v2_5 {
    background-color:#063
  }
  .vtair10 {
    background-color:#066
  }
</style> -->
