<?php
  if ($tb_jbrowse) {
    

$quoted_search = 0;
if (preg_match('/^".+"$/', $name)) {
  $quoted_search = 1;
}

$jb_query = test_input2($gene_name);

// var_dump($jb_query);



echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#jb_section" aria-expanded="true"><i class="fas fa-sort" style="color:#229dff"></i> Genome Browser</div>';
echo '<div id="jb_section" class="collapse show">';

$jb_gene_name = $jb_query;

// $jb_dataset = $annot_file;
$jb_dataset = "annona";

    
    echo "<a class=\"float-right jbrowse_link\" href=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\">Full screen</a>";
    echo "<iframe class=\"jb_iframe\" src=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\" name=\"jbrowse_iframe\">";
    echo "<p>Your browser does not support iframes.</p> </iframe>";
    echo '</div>';



  }
?>


<style>
  .jb_iframe {
    border: 1px solid rgb(80, 80, 80);
    height: 300px;
    width: 100%;
    margin-right: 20px;
  }
</style>
