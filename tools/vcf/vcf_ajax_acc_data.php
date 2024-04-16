<?php

# TRY TO GET THESE FILES FROM BACK END
$tabix_cmd = $_POST["tabix_cmd"];
$acc_path = $_POST["acc_path"];

$html_array = array();

$tabix_out = shell_exec($tabix_cmd);
$lines_array = explode("\n",$tabix_out);
$tabix_out = "";

$header_array = [];
if ( file_exists($acc_path)) {
  $header_file = file_get_contents($acc_path);
  $header_array = explode("\t",$header_file);
}


array_push($html_array,"<thead><tr><th>ACC</th><th>GT</th><th>AD</th><th>DP</th><th>GQ</th><th>PGT</th><th>PID</th><th>PL</th></tr></thead>\n");
array_push($html_array,"<tbody>\n");


foreach (array_filter($lines_array) as $vcf_line) {
  
  $data_array = explode("\t",$vcf_line);
  $vcf_line = "";
  
  $index = 0;
  
  foreach ($data_array as $col) {
  //$snp_col_array = preg_grep("/^[0-1]\/1\:/", $data_array);
  //$data_array = [];
  
    if (preg_match("/^[0-1]\/1\:/", $col)) {
    // foreach ($snp_col_array as $index => $value) {
      // if ($index >8) {
  //        echo "<tr><td>$header_array[$index]</td>\n";
      array_push($html_array,"<tr><td><a href=\"#\">$header_array[$index]</a></td>");
    
        $acc_snp_array = explode(':',$col);
        // echo "<tr>";
  
        foreach ($acc_snp_array as $val) {
          //echo "<td>$val</td>\n";
          array_push($html_array,"<td>$val</td>");
        }
        //echo "</tr>\n";
        array_push($html_array,"</tr>\n");
      // }
      
      
    }
    
    $index++;
  }

  array_push($html_array,"</tbody>\n");
  
  

$data_array = [];
}

$header_array=[];
$lines_array=[];

  //rsort($file_array);
  //echo "hello";
  echo json_encode($html_array);



?>
