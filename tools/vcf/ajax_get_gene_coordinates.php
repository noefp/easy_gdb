<?php
  
  $gene_name = $_POST["query_gene"];
  $gff_file = $_POST["gff_file"];
  
  $gff_array = [];
  
  if ( file_exists($gff_file) ) {
    $grep_cmd = "zgrep '$gene_name' $gff_file";
    $grep_out = shell_exec($grep_cmd);
  }

  $gff_lines = explode("\n",$grep_out);
  foreach (array_filter($gff_lines) as $gff_line) {
    $col = explode("\t",$gff_line);
    
    array_push($gff_array,"<tr><td>$col[0]</td><td>$col[2]</td><td>$col[3]</td><td>$col[4]</td><td>$col[6]</td><td>$col[8]</td></tr>");
  }
  //echo $gff_array;
  echo json_encode($gff_array)
?>
   