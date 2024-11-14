<?php
if ("$root_path/jbrowse/data/") {

  $quoted_search = 0;
  if (preg_match('/^".+"$/', $name)) {
    $quoted_search = 1;
  }

  $jb_gene_name = test_input2($gene_name);

  $counter = 0;
  $jb_dataset = "easy_gdb_sample";

  if ($dh = opendir("$root_path/jbrowse/data/")){
    //iterate all files in dir
    while ( ($file_name = readdir($dh)) !== false ){ 
    
      if (!preg_match('/^\./', $file_name) ) { //discard hidden files
        if (is_dir("$root_path/jbrowse/data/".$file_name)){ // get dirs and print categories
          $jb_dataset = $file_name;
          $counter++;
        }
      }
    
    }
  }

  // if ($counter >1) {
  //   echo "<p>Several JBrowse datasets were found</p>";
  // }
  
  if ($counter == 1) {
    echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#jb_section" aria-expanded="true"><i class="fas fa-sort" style="color:#229dff"></i> Genome Browser</div>';
    echo '<div id="jb_section" class="collapse show">';
  
    echo "<a class=\"float-right jbrowse_link\" href=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\">Full screen</a>";
    echo "<iframe class=\"jb_iframe\" src=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\" name=\"jbrowse_iframe\">";
    echo "<p>Your browser does not support iframes.</p> </iframe>";
    echo '</div>';
  }
  
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
