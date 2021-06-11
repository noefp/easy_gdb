<?php
  if ($tb_jbrowse) {
    
    $jb_query = "SELECT * FROM species WHERE species_id='".$species_id."'";
    $jb_res = pg_query($jb_query) or die("The gene $gene_name was not found in the database. Most probably this gene was not associated to a gene from the current version.");
    $gene_row = pg_fetch_array($jb_res,0,PGSQL_ASSOC);
    $jb_dataset = $gene_row["jbrowse_folder"];
    
    echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#jb_section" aria-expanded="true">Genome Browser</div>';
    echo '<div id="jb_section" class="collapse show">';

    $jb_gene_name = $gene_name;
    // $jb_gene_name = $gene_name_displayed;
    // if (preg_match('/\.\d$/',$gene_name_displayed) ) {
      // $jb_gene_name = preg_replace('/\.\d+$/','',$gene_name_displayed);
    // }
    
    // $jb_dataset = "easy_gdb_sample";
    
    echo "<a class=\"float-right jbrowse_link\" href=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\">Full screen</a>";
    echo "<iframe class=\"jb_iframe\" src=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\" name=\"jbrowse_iframe\">";
    echo "<p>Your browser does not support iframes.</p> </iframe>";
  }
?>
<!-- </div> -->


<style>
  .jb_iframe {
    border: 1px solid rgb(80, 80, 80);
    height: 300px;
    width: 100%;
    margin-right: 20px;
  }
</style>
