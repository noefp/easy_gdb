
<?php

  $jb_gene_name = test_input2($gene_name);
  $annot_filename = preg_replace('/.+\//','',$annot_file);
  
  if ( file_exists($json_files_path."/tools/annotations_conf.json") ) {
    $ann_json_file = file_get_contents("$json_files_path/tools/annotations_conf.json");
    $annot_hash = json_decode($ann_json_file, true);
  }
    
  if ($annot_hash[$annot_filename]) {
    if ($annot_hash[$annot_filename]["jbrowse"]) {
      
      $jb_link = $annot_hash[$annot_filename]["jbrowse"];
    
      if (preg_match('/\{gene_name\}/', $jb_link, $match)) {
        $jb_link = preg_replace('/\{gene_name\}/',$jb_gene_name,$jb_link);
      }
    
      echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#jb_section" aria-expanded="true"><i class="fas fa-sort" style="color:#229dff"></i> Genome Browser</div>';
      echo '<div id="jb_section" class="collapse show">';
      echo "<a class=\"float-right jbrowse_link\" href=\"$jb_link\">Full screen</a>";
      echo "<iframe class=\"jb_iframe\" src=\"$jb_link\" name=\"jbrowse_iframe\">";
      echo "<p>Your browser does not support iframes.</p> </iframe>";
      echo '</div>';
    }
  }
  else if ("$root_path/jbrowse/data/") {
      
    $counter = 0;
    $jb_dataset = "";

    if ($dh = opendir("$root_path/jbrowse/data/")){
      //iterate all files in dir
      while ( ($file_name = readdir($dh)) !== false ){ 
    
        if (!preg_match('/^\./', $file_name) ) { //discard hidden files
          if (is_dir("$root_path/jbrowse/data/".$file_name)){ // get dirs and print categories
            $jb_dataset = $file_name;
            $counter++;
          }
        }
          
      } //end while
    } //end if
    
    if ($counter == 1) {
      echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#jb_section" aria-expanded="true"><i class="fas fa-sort" style="color:#229dff"></i> Genome Browser</div>';
      echo '<div id="jb_section" class="collapse show">';

      echo "<a class=\"float-right jbrowse_link\" href=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\">Full screen</a>";
      echo "<iframe class=\"jb_iframe\" src=\"/jbrowse/?data=data%2F$jb_dataset&loc=$jb_gene_name&tracks=DNA%2Ctranscripts&highlight=\" name=\"jbrowse_iframe\">";
      echo "<p>Your browser does not support iframes.</p> </iframe>";
      echo '</div>';
    }
    
  } // end else if

  // else if ($counter >1) {
  //   echo "<p>Several JBrowse datasets were found</p>";
  // }

?>

<style>
  .jb_iframe {
    border: 1px solid rgb(80, 80, 80);
    height: 300px;
    width: 100%;
    margin-right: 20px;
  }
</style>
