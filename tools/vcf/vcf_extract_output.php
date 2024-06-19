<!-- HEADER -->
<?php include_once realpath("../../header.php");?> 

<!-- RETURN AND HELP-->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a href="vcf_extract_input.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>

<center><h1>Variant Explorer</h1></center>

<br>

<!-- HTML -->
<div class="page_container">

<?php
  //get input variables
  
  $vcf_chr = $_GET["vcf_chr"];
  $vcf_start = trim($_GET["vcf_start"]);
  $vcf_end = trim($_GET["vcf_end"]);
  
  
  
  if (file_exists("$vcf_path/vcf.json")) {
    $vcf_json_file = file_get_contents("$vcf_path/vcf.json");
    $vcf_hash = json_decode($vcf_json_file, true);
  }

  $passport_dir = $vcf_hash["passport_folder"];
  $jb_data_folder = $vcf_hash["jb_data_folder"];
  $vcf_chr_file = $chr_file_array = $vcf_hash["chr_files"][$vcf_chr];

  
  // run tabix
  $tabix_command = "tabix $vcf_path"."/$vcf_chr_file ".$vcf_chr.":".$vcf_start."-".$vcf_end;
  //echo "<br><p>command: $tabix_command</p>";
  ini_set( 'memory_limit', '1024M' );
  $tabix_out = shell_exec($tabix_command);
  
  //echo "<p>output: $tabix_out</p>";
  
  $lines_array = explode("\n",$tabix_out);
  $tabix_out = "";
  
  //print SNPs table
  echo '<div class="row">';
  echo '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" style="overflow: scroll">';
  
  echo "<div class=\"card bg-light text-dark\">";
  echo "  <div class=\"card-body\"><b>SNPs found</b> in $vcf_chr $vcf_start-$vcf_end</div>";
  echo "</div><br>";
  
  echo "<table id=\"snp_table\" class=\"table table-striped table-bordered\" style=\"line-height: 1; font-size:14px\">\n";
  echo "<thead><tr><th>CHR</th><th>POS</th><th>ID</th><th>REF</th><th>ALT</th><th>SNPeff</th><th>ACCs</th></tr></thead>\n";
  echo "<tbody>\n";
  
  $table_counter = 1;
  
  foreach (array_filter($lines_array) as $vcf_line) {
    
    $data_array = explode("\t",$vcf_line);
    
    $snp_info = array_slice($data_array,0,8);
    $data_array = [];
    
    echo "<tr>";
  
    foreach ($snp_info as $index => $snp_val) {
      
      if ($index == 1) {
        $jbrowse_link = "/jbrowse/?data=data%2F$jb_data_folder&loc={chr}%3A{start}..{end}";
        $jbrowse_link = str_replace("{chr}",$snp_info[0],$jbrowse_link);
        $jbrowse_link = str_replace("{start}",$snp_info[1]-50,$jbrowse_link);
        $jbrowse_link = str_replace("{end}",$snp_info[1]+50,$jbrowse_link);
        
        echo "<td><a href=\"$jbrowse_link\" target=\"_blank\">$snp_val</a></td>";
      } else if ($index == 5 || $index == 6) {
      } else if ($index == 2) {
        echo "<td style='font-size:12px'>Ca1_$snp_info[1]</td>";
      } else if ($index == 7) {
        $snp_eff = preg_replace("/.+ANN=/",'',$snp_val);
        $eff_array = explode("|",$snp_eff);
        
        echo "<td style='font-size:12px;width:0%'>$eff_array[4] $eff_array[1]</td>";
        
      } else {
        echo "<td>$snp_val</td>";
      }
    }
    echo "<td><a class=\"acc_link\" name=\"snp_pos\" value=\"$snp_info[1]\"\>ACC Info</a></td>";
    echo "</tr>\n";
  }
  echo "</tbody></table>\n";
  
  echo "</div>\n";
  
  $snp_info = [];
  $lines_array = [];
  
?>


    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
    
      <table id="selected_snp_table" class="table table-bordered" style="line-height: 1; font-size:14px">
        <thead><tr><th>CHR</th><th>POS</th><th>ID</th><th>REF</th><th>ALT</th><th>SNPeff</th><th>ACCs</th></tr></thead>
        <tbody>
          <tr id="selected_snp"></tr>
        </tbody>
      </table>
      
      <table id="acc_table" class="table table-striped table-bordered"  style="line-height: 1; font-size:14px">
        <thead><tr><th>Click on ACC info to visualize accession details</th></tr></thead>
      </table>
      
    </div>
    
    
  </div> <!-- end of row -->

  <br>
  <br>
</div> <!-- page_container -->
<!-- END HTML -->

<!-- JS DATATABLE -->
<script type="text/javascript">
  //var counter = 0;
  
  $('.acc_link').click(function () {
    //alert( $(this).parent().parent().html() );
    
    $('#selected_snp').html($(this).parent().parent().html());

    var snp_pos = $(this).attr('value');
    
    
    
    var tabix_cmd = "<?php echo "tabix $vcf_path"."/$vcf_chr_file"." ".$vcf_chr.":" ?>"+snp_pos+"-"+snp_pos;
    //var acc_path = "<?php //echo "$root_path"."/".$downloads_path."/vcf/acc_header.txt" ?>";
    var vcf_file = "<?php echo "$vcf_path"."/$vcf_chr_file" ?>";
    var passport_dir = "<?php echo "$passport_dir" ?>";
   
    //alert("acc_path: "+acc_path );

    //call PHP file ajax_get_names_array.php to get the gene list to autocomplete from the selected dataset file
    function ajax_call(tabix_cmd,vcf_file,passport_dir) {
    
      jQuery.ajax({
        type: "POST",
        url: 'vcf_ajax_acc_data.php',
        data: {'tabix_cmd': tabix_cmd, 'vcf_file': vcf_file, 'passport_dir': passport_dir},

        success: function (html_array) {
          //alert("hi: "+html_array);
          
          var table_rows = JSON.parse(html_array);
          
          
          if ($.fn.DataTable.isDataTable("#acc_table")) {
            $('#acc_table').DataTable().clear().destroy();
          }
            
          $('#acc_table').html(table_rows.join());
            
            
            $("#acc_table").dataTable({
              dom:'Bfrtlpi',
              "oLanguage": {
                "sSearch": "Filter by:"
                },
              buttons: [
                'copy', 'csv', 'excel',
                  {
                    extend: 'pdf',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                  },
                 'colvis'
                ]
              });

            $("#acc_table_filter").addClass("float-right");
            $("#acc_table_info").addClass("float-left");
            $("#acc_table_paginate").addClass("float-right");
            //}
          //counter = 1;
          
        }
      });
    
    }; // end ajax_call
    
    ajax_call(tabix_cmd,vcf_file,passport_dir);
    // return true;
  });
  
  //    dom:'Bfrtlpi',

  
  $("#snp_table").dataTable({
    dom:'Bfrtlpi',
    "oLanguage": {
      "sSearch": "Filter by:"
      },
    buttons: [
      'copy', 'csv', 'excel',
        {
          extend: 'pdf',
          orientation: 'landscape',
          pageSize: 'LEGAL'
        },
       'colvis'
      ]
    });

  $("#snp_table_filter").addClass("float-right");
  $("#snp_table_info").addClass("float-left");
  $("#snp_table_paginate").addClass("float-right");
  
</script>

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
  
</style>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>