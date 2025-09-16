<?php include_once realpath("../../header.php");?> 

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="#" target="blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a class="float-left pointer_cursor" style="text-decoration: underline;" onClick="history.back()"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>
<center><h1>Variant Explorer</h1></center>

<!-- HTML -->
<div class="page_container">

<?php
  //get input variables
  $snp_id = $_GET["snp_id"];
  $vcf_dir=$_GET["snp_file"];
  

  // echo "<p>SNP ID: $vcf_dir</p>";
  
  
  //GET JBROWSE DATA FOLDER NAME!!!!!!!!!!!!
  
  preg_match('/(\d+)_(\d+)/', $snp_id, $matches);
  $vcf_chr = 'chr'.$matches[1];
  $vcf_pos = $matches[2];
  
  
  // if (file_exists("$vcf_path/vcf.json")) {
    $vcf_json_file = file_get_contents($json_files_path."/tools/vcf.json");
    $vcf_hash = json_decode($vcf_json_file, true);
  // }

  if($vcf_dir != "") {
    $vcf_file = $vcf_hash[$vcf_dir]["chr_files"][$vcf_chr];
    $jb_data_folder = $vcf_hash[$vcf_dir]["jb_data_folder"];
    $snp_eff_subtable=$vcf_hash[$vcf_dir]["snp_eff_subtable"];

    $vcf_path_file= "$vcf_path/$vcf_dir/$vcf_file";
  }else { 
    $vcf_file = $vcf_hash["chr_files"][$vcf_chr];
    $jb_data_folder = $vcf_hash["jb_data_folder"];
    $snp_eff_subtable=$vcf_hash["snp_eff_subtable"];

    $vcf_path_file= "$vcf_path/$vcf_file"; 

  }
  // get header line with all the accessions from the vcf file
  $header_array = [];
  $data_array = [];



  if ( file_exists($vcf_path_file)) {
    
    $header_cmd = "zgrep -m 1 \"^#CHROM\" $vcf_path_file";
    
    //echo "<p>header_cmd: $header_cmd</p>";
    
    $header_file = shell_exec($header_cmd);
    $header_array = explode("\t",$header_file);
    
    // echo "<p>output: $header_file</p>";
    
    $header_file = "";
    
    // run tabix
    $tabix_command = "tabix $vcf_path_file ".$vcf_chr.":".$vcf_pos."-".$vcf_pos;
    
    //echo "<br><p>command: $tabix_command</p>";
    ini_set( 'memory_limit', '1024M' );
    $tabix_out = shell_exec($tabix_command);
  
    //echo "<p>output: $tabix_out</p>";
  
    $data_array = array_filter(explode("\n",$tabix_out));
    $tabix_out = "";
  }

  //print SNPs table
  if( count($data_array) == 0) {
    echo "<div class=\"alert alert-danger\" role=\"alert\" style=\"text-align:center; margin-top:10px\"> <b>No SNPs found</b> in $snp_id</div>";
  }
  else
  {
    echo "<br><div id=\"load\" class=\"loader\"></div>";

    echo '<div id="table_container" style="display:none; margin-top:20px">';

    echo "<div class=\"card bg-light text-dark\">";
    echo "  <div class=\"card-body\"><b>SNP found</b> for $snp_id</div>";
    echo "</div><br>";
    
    echo "<table id=\"snp_table\" class=\"table table-striped table-bordered\" style=\"line-height: 1; font-size:14px\">\n";
    echo "<thead><tr><th>CHR</th><th>POS</th><th>ID</th><th>REF</th><th>ALT</th>";
    if($snp_eff_subtable)
    {
      echo "<th>SNPeff</th>";
    }
    echo "<th>ACC</th>\n";
    
    //add header extra columns
    $data_columns = explode("\t",$data_array[0]);
    $acc_table_header = explode(':',$data_columns[8]);
    $data_array = [];
  
  //echo "<p>acc header: $data_columns[8]</p>";
  
    foreach ($acc_table_header as $th) {
      echo "<th>$th</th>";
    }
    
    echo "</tr></thead>\n";
  
  
  
    echo "<tbody>\n";
    
    $html_array = [];
    
    
    echo "<tr>\n";
  
  
    // foreach (array_filter($data_array) as $vcf_line) {
      
      // $data_array = explode("\t",$vcf_line);
      
    $snp_info = array_slice($data_columns,0,8);
      
      //array_push($html_array,"<tr>\n");
    
    foreach ($snp_info as $index => $snp_val) {

      if ($index == 0 || $index == 3 || $index == 4) {
        array_push($html_array,"<td>$snp_val</td>");
      }
      elseif ($index == 1) {
        $jbrowse_link = "/jbrowse/?data=data%2F$jb_data_folder&loc={chr}%3A{start}..{end}";
        $jbrowse_link = str_replace("{chr}",$snp_info[0],$jbrowse_link);
        $jbrowse_link = str_replace("{start}",$snp_info[1]-50,$jbrowse_link);
        $jbrowse_link = str_replace("{end}",$snp_info[1]+50,$jbrowse_link);
        
        //echo "<td><a href=\"$jbrowse_link\">$snp_val</a></td>";
        array_push($html_array,"<td><a href=\"$jbrowse_link\" target=\"_blank\">$snp_val</a></td>");
      }
      elseif ($index == 2) {
        
        //echo "<td style='font-size:12px'>Ca1_$snp_info[1]</td>";
        array_push($html_array,"<td style='font-size:12px'>$snp_id</td>");
        // array_push($html_array,"<td style='font-size:12px'>Ca1_$snp_info[1]</td>");
        
      } elseif ($index == 7 && $snp_eff_subtable) {
        $snp_eff = preg_replace("/.+ANN=/",'',$snp_val);
        $snp_eff_array = explode(",",$snp_eff); // in case there are multiple annotations separated by commas
        
        $eff_array_data = array_map(function($eff) {
          return explode("|",$eff);
        }, $snp_eff_array);

        $snp_eff_array_count = count($snp_eff_array);

        $eff_array_length = count(explode("|",$snp_eff_array[0]));

       
          $snpeff_table = "<table id='subtable'  style=\"font-size:11px; width:100%\">";
          $snpeff_table.="<tr>";
          foreach($snp_eff_subtable["head"] as $head)
          {
            $snpeff_table.="<th>$head</th>";
          }
          $snpeff_table .= "</tr>";

        for($x=0;$x<$snp_eff_array_count;$x++)
        {
          $snpeff_table .="<tr>";
          foreach($snp_eff_subtable["data"] as $data)
          {
            $data = $data-1;
            $snpeff_table .="<td>".$eff_array_data[$x][$data]."</td>";
          }
          $snpeff_table .="</tr>";
        }

          $snpeff_table .= "</table>";
        
        //echo "<td style='font-size:12px;width:0%'>$eff_array[4] $eff_array[1]</td>";
        array_push($html_array,"<td style='padding:0 !important'>$snpeff_table</td>");
        
      } 
    } 
    
    $acc_info = array_slice($data_columns,9,-1);
    $head_info = array_slice($header_array,9,-1);
  
    // find all accessions with variants
    $acc_filtered_data = preg_grep("/^[0-1]\/1\:/", $acc_info);
    $data_columns = [];
  
    foreach ($acc_filtered_data as $col_index => $col_value) {
      echo implode("",$html_array);
      $acc_array = explode(':',$col_value);
      $acc_name = $head_info[$col_index];
      
      echo "<td><a href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=$vcf_dir&acc_id=$acc_name\">$acc_name</a></td>";
      //<a href=\"/easy_gdb/tools/passport/03_passport_and_phenotype.php?pass_dir=Chickpea_10K&row_num=$index\">$header_array[$index]</a>
      
      foreach ($acc_array as $acc_value) {
        echo "<td>$acc_value</td>";
      }
      echo "</tr>\n";
      
    }
    
    //get acc from vcf header
    //echo "<td><a class=\"acc_link\" name=\"snp_pos\" value=\"$snp_info[1]\"\>ACC Info</a></td>";
    
    
    
    //array_push($html_array,"</tr>\n");
    
  // }
  echo "</tbody></table>\n";
  }
  echo "</div>\n";
  
  
  
  $snp_info = [];
  $data_columns = [];
  
?>
      
<!-- </div> end of row -->
<br>
<br>
</div> <!-- page_container -->
<!-- END HTML -->

<!-- CSS -->
<style>
  .acc_link {
    cursor:pointer;
  }
  
  #selected_snp_table th {
    padding: .25rem;
  }
  
  #selected_snp_table td {
    padding: .25rem;
  } 

 #subtable th,  #subtable td {
    padding-left: 10px !important;
    padding-right: 10px !important;
    border: 1px solid #ddd !important;
  }
</style>


<!-- JS DATATABLE -->

<script src="../../js/datatable.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $("#load").hide();
  $("#table_container").show();
  // Initialize the DataTable with subtable functionality
  datatable_with_subtable("#snp_table","#subtable");

  // Add classes to the DataTable controls for styling
  $("#snp_table_filter").addClass("float-right");
  $("#snp_table_info").addClass("float-left");
  $("#snp_table_paginate").addClass("float-right");
});  
</script>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>