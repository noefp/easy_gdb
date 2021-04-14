<?php
  if ($tb_jbrowse) {
    echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#jb_section" aria-expanded="true">Genome Browser</div>';
    echo '<div id="jb_section" class="collapse show">';

    $jb_gene_name = $gene_name_displayed;
    // if (preg_match('/\.\d$/',$gene_name_displayed) ) {
      // $jb_gene_name = preg_replace('/\.\d+$/','',$gene_name_displayed);
    // }

    echo "<a class=\"float-right jbrowse_link\" href=\"/jbrowse/?loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\">Full screen</a>";
    echo "<iframe class=\"jb_iframe\" src=\"/jbrowse/?loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\" name=\"jbrowse_iframe\">";
    echo "<p>Your browser does not support iframes.</p> </iframe>";
  }
?>
</div>


<style>
  .jb_iframe {
    border: 1px solid rgb(80, 80, 80);
    height: 300px;
    width: 100%;
    margin-right: 20px;
  }
</style>

<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#annot_section" aria-expanded="true">
  Functional descriptions
</div>

<div id="annot_section" class="collapse show">
  <br>

<?php

// $query = "SELECT * FROM annotation JOIN gene_annotation USING(annotation_id) JOIN gene USING(gene_id) WHERE gene_id='".pg_escape_string($gene_id)."'";
$query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) WHERE gene_id='".pg_escape_string($gene_id)."'";
// $query = "SELECT * FROM gene FULL OUTER JOIN gene_annotation USING(gene_id) FULL OUTER JOIN annotation USING(annotation_id) FULL OUTER JOIN annotation_type USING(annotation_type_id) WHERE gene_id='".pg_escape_string($gene_id)."'";

$res = pg_query($query) or die('Query failed: ' . pg_last_error());


// Printing results in HTML
echo "<table class=\"table annot_table\">\n<tr><th>Gene ID</th><th>Description</th><th>Source</th></tr>\n";


while ($line = pg_fetch_array($res, null, PGSQL_ASSOC)) {
     $q_term = $line["annotation_term"];
     $q_desc = $line["annotation_desc"];
     $annot_type = $line["annotation_type"];
     $q_link = "#";

     if ($annot_type == "TAIR10") {
       $q_link = 'http://www.arabidopsis.org/servlets/TairObject?type=locus&name='.preg_replace('/\.\d$/','',$q_term);
     }
     if ($annot_type == "Araport11") {
       $q_link = 'http://www.arabidopsis.org/servlets/TairObject?type=locus&name='.preg_replace('/\.\d$/','',$q_term);
     }
     if ($annot_type == "SwissProt") {
       $q_link = 'http://www.uniprot.org/uniprot/'.preg_replace('/sp\|(.+)\|.+/','$1',$q_term);
     }
     if ($annot_type == "InterPro") {
       $q_link = 'https://www.ebi.ac.uk/interpro/entry/InterPro/'.$q_term.'/';
     }
     if ($annot_type == "NCBI") {
       $q_link = 'https://www.ncbi.nlm.nih.gov/protein/'.$q_term;
     }

     echo "<tr><td><a href=\"$q_link\" target=\"_blank\">$q_term</a></td><td>$q_desc</td><td>$annot_type</td></tr>\n";
}



// echo "<tr><td><a href=\"/jbrowse/?loc=$jb_gene_name&tracks=DNA%2Cgene%20models%20v0.61\" target=\"_blank\">$gene_name_displayed</a></td><td>Genome browser</td><td>OliveTree DB</td></tr>\n";

echo "</table>\n\n";

// Free resultset
pg_free_result($res);
?>

<br>
</div>
