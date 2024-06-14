<!-- HEADER -->
<?php 
  include_once realpath("../../header.php");
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
?>

<!-- HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php"><i class="fa fa-info" style="font-size:20px;color:#229dff"></i> Help</a>
</div>
<br>
<h3 class="text-center">Passport Search</h3>


<?php
  function read_passport_file($passport_path,$passport_file,$acc_header_name) {
    
    $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$passport_file);
    $dataset_name = str_replace("_"," ",$dataset_name);
    $frame_id = preg_replace('/[. ]/',"",$passport_file);
    
    // $link_name = preg_replace('/\s|\.|\d/', '', $dataset_name);
    
    // echo "<h4>$passport_path/$passport_file</h4>";
    
    if (file_exists("$passport_path/$passport_file")) {
      
      //echo "<h4>$dataset_name</h4>";
    
      echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#$frame_id\" aria-expanded=\"true\">";
        echo "<i class=\"fas fa-sort\" style=\"color:#229dff\"></i> $dataset_name";
      echo "</div>";

      echo "<div id=\"$frame_id\" class=\"collapse show\" style=\"border:2px solid #666; padding-top:7px\">";
      
      
      
      // echo "<h4>$passport_path/$passport_file</h4>";
      
      $pass_array = file("$passport_path/$passport_file");
      // $pass_array = explode("\n", $pass_content);
      $header = array_shift($pass_array);
      $header_array = explode("\t", $header);
      
      // echo "passport header: $header";
      
      $no_spc_file = str_replace(" ","\ ","$passport_path/$passport_file");
      
      // foreach ($header_array as $key => $value) {
      //
      //   $col_index = $key + 1;
      //
      //   $shell_cmd = "tail -n +2 $no_spc_file | cut -f $col_index | sort -u";
      //   $shell_res = shell_exec($shell_cmd);
      //
      //   $is_numeric = 1;
      //
      //   // echo "hello!!! $shell_res <br>";
      //
      //   if ( preg_match("/[A-Za-z]/", $shell_res) ) {
      //     $is_numeric = 0;
      //   }
      //   $shell_array = explode("\n",$shell_res);
      //   $shell_res = "";
        
        echo "<div class=\"row\" style=\"margin:0px\">";
      
        echo "<div class=\"col-xs-12 col-sm-12 col-md-6 col-lg-6\">";
        echo "<select class=\"sel_opt\" id=\"sel1\" name=\"$no_spc_file\" class=\"form-control\">";
        foreach ($header_array as $index => $value) {
          if ($value != $acc_header_name) {
            echo "<option name=\"$index\">$value</option>";
          }
        }
        echo "</select>";
        
        echo "</div>";
      
          
        echo "<div class=\"col-xs-12 col-sm-12 col-md-6 col-lg-6\">";
            echo "<label for=\"sel2\">Filter by:</label>";
            
        // if ($is_numeric) {
          // echo "<div class=\"form-group\">";
          //   echo "<div class=\"form-inline\">";
          //
          //     echo "<select id=\"sel1\" class=\"form-control\">";
          //       echo "<option name=\"gt\">></option>";
          //       echo "<option name=\"gteq\">>=</option>";
          //       echo "<option name=\"eq\">=</option>";
          //       echo "<option name=\"leq\"><=</option>";
          //       echo "<option name=\"lt\"><</option>";
          //     echo "</select>";
               //echo "<input id=\"numeric_input\" type=\"search_box\" class=\"form-control\" name=\"\" style=\"display:none width:150px; background-color:#efefef; margin-left: 20px\" placeholder=\"0\">";
          //
          //   echo "</div>";
          // echo "</div>";

        // } else {
          echo "<div class=\"form-group\">";
          echo "<div class=\"form-inline\">";
          
            echo "<select multiple id=\"sel2\" class=\"form-control\" style=\"width:100%\">";
          
          // foreach (array_filter($shell_array) as $option) {
          //   echo "<option name=\"$option\">$option</option>";
          // }
            echo "</select>";
            echo "<input id=\"numeric_input\" type=\"search_box\" class=\"form-control\" name=\"\" style=\"display:none; width:150px; background-color:#efefef; margin-left: 20px\" placeholder=\"0\">";
          
          echo "</div>";
          echo "</div>";
        // }
              
            echo "</div>"; // col
          echo "</div>"; // row
      
      //} // foreach col
      
      
      
      echo "</div>";
      
    } // if file exist
    
  }
?>


<!-- INPUT FORM -->
<div class="form margin-20">
  <div style="margin:auto; max-width:1200px">

      <!-- FORM OPPENED -->
    <form id="egdb_passport_form" action="passport_search_output.php" method="get">
      <div class="form-group">
        <label for="search_box" style="font-size:16px">Insert an accession ID or passport keywords</label>
        <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button>
        <input type="search_box" class="form-control" id="search_file_box" name="search_keywords" style="border-color: #666">
      </div>
      <br>
      <button type="submit" class="btn btn-info float-right" style="margin-top: -5px">Search</button>
      <br>
      <br>
      <br>
    </form>

<br>
<!-- <h3 class="text-center">Advanced Search</h3>

<form id="egdb_passport_form" action="passport_search_output.php" method="get">

  <div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6">

      <div class="form-group">
        <label for="autocomplete_acc" >Find your accession ID:</label>

        <div class="input-group mb-3">
          <input id="autocomplete_acc" type="text" class="form-control form-control-lg" placeholder="Accession ID">
          <div class="input-group-append">
            <button id="add_gene_btn" class="btn btn-success"><i class="fas fa-angle-double-right" style="font-size:28px;color:white"></i></button>
          </div>
        </div>

      </div>

    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">

      <label for="InputGenes">Paste a list of accession IDs</label>
      <textarea class="form-control" id="InputGenes" rows="6" name="gids">
      </textarea>
      <br>

    </div>
  </div> -->
      
<?php
//
//   $all_datasets = get_dir_and_files($passport_path); // find dirs in passport path
//   asort($all_datasets);
//
// //  $dir_counter = 0;
//
// //  foreach ($all_datasets as $one_dir) {
// //    if (is_dir($passport_path."/".$one_dir)){ // get dirs and print categories
// //      $dir_counter++;
// //    }
// //  }
//
//   //category organization
// //  if ($dir_counter) {
//
//     foreach ($all_datasets as $dir_or_file) {
//       if (is_dir($passport_path."/".$dir_or_file)){ // get dirs and print categories
//
//         $dir_name = str_replace("_"," ",$dir_or_file);
//         echo "<h4>$dir_name</h4>";
//
// //        $pass_files = get_dir_and_files($passport_path."/".$dir_or_file); // call the function
// //        sort($pass_files);
//
// //        foreach ($pass_files as $passport_file) {
//
//           //echo "passport_path: $passport_path/$dir_or_file/passport.json<br>";
//
//           // get info from passport.json
//           if ( file_exists("$passport_path/$dir_or_file/passport.json") ) {
//             $pass_json_file = file_get_contents("$passport_path/$dir_or_file/passport.json");
//             $pass_hash = json_decode($pass_json_file, true);
//
//             $passport_file = $pass_hash["passport_file"];
//             $phenotype_file_array = $pass_hash["phenotype_files"];
//             $unique_link = $pass_hash["acc_link"];
//
//
//
//           // if ( !preg_match('/\.php$/i', $passport_file) && !is_dir($passport_path.'/'.$dir_or_file.'/'.$passport_file) &&  !preg_match('/\.json$/i', $passport_file) && file_exists($passport_path.'/'.$dir_or_file.'/'.$passport_file)   ) {
//
//             // echo "passport_path: $passport_path<br>";
//             // echo "dir_or_file: $dir_or_file<br>";
//             // echo "passport_file: $passport_file<br>";
//             // echo "unique_link: $unique_link<br>";
//
//             read_passport_file("$passport_path/$dir_or_file",$passport_file,$unique_link);
//
//             foreach ($phenotype_file_array as $phenotype_file) {
//               read_passport_file("$passport_path/$dir_or_file",$phenotype_file,$unique_link);
//             }
//           }
//
//           // }//if preg_match
//         }//foreach all_dir
//       }//if is_dir
// //    }// foreach dir
// //  }//if dir_counter
// //  else {
//     // without categories
// //    read_passport_file($passport_path,$passport_file);
// //  }
?>
    <!-- <br>
    <button type="submit" class="btn btn-info float-right" style="margin-top: -5px">Search</button>
    <br>
    <br>
    <br>
    </form> -->
  </div>
</div>


<!-- ERROR BANNER -->
<div class="modal fade" id="no_gene_modal" role="dialog">
  <div class="modal-dialog modal-sm">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="text-align: center;">ERROR</h4>
      </div>
      <div class="modal-body">
        <div style="text-align: center;">
          <p id="search_input_modal"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>


<!-- IS BETTER TO ADD TO THE GENERAL CSS -->
<style>  
  .info_icon {
    background-color:#4387FD;
    border-radius:20px;
    vertical-align: top;
    border:0px;
    display:inline-block;
    color:#ffffff;
    font-family:"Georgia",Georgia,Serif;
    font-size:12px;
    font-weight:bold;
    font-style:normal;
    width:18px;
    height:18px;
    line-height:0px;
    text-align:center;
  }
  .info_icon:hover {
    background-color:#5EA1FF;
    color:#0000CC;
  }

  .info_icon:active {
    position:relative;
    top:1px;
  }
</style>


<!-- JAVASCRIPT -->
<script> 
$(document).ready(function () {


  function get_ajax_options(col_index,query_file) {
    //alert("file: "+query_file+", col_index: "+col_index);

    jQuery.ajax({
      type: "POST",
      url: 'passport_ajax.php',
      data: {'col_index': col_index, 'query_file': query_file},

      success: function (opt_array) {
        //alert("opt_lines: "+opt_array);

        var opt_lines = JSON.parse(opt_array);

        //alert("opt_lines: "+opt_lines[0]);
        $("#sel2").html(opt_lines.join("<br>"));

        if (opt_lines[0] == '<option name=\"gt\">></option>') {
          //alert("opt_lines: "+opt_lines[0]);

          $("#numeric_input").css("display","inline");
          $("#sel2").css("width",70+"px");
          $("#sel2").removeAttr("multiple");

          //$("#sel2").html(opt_lines.join("<br>"));

        } else {
          $("#numeric_input").css("display","none");
          $("#sel2").css("width","100%");
          $("#sel2").attr("multiple","multiple");


          //$("#sel2").html(opt_lines.join("<br>"));
        }


      }
    });

  }; // end ajax_call
  
  var file_path1 = "<?php echo "$passport_path" ?>";
  //alert("file1: "+file_path1);

  $('.sel_opt').change(function() {
    
    var column_name = $('.sel_opt').val();
    var passport_full_path = $('.sel_opt').attr("name");
    
    var col_index = $(this).find('option:selected').attr("name");
    //var gff_file = "<?php //echo "$root_path"."/"."$downloads_path"."/vcf/Car.genes.gff.zip"; ?>";
    //alert("Selected: "+column_name+" "+col_index+" "+file_path1);
    //alert("file2: "+file_path1+", "+passport_dir);
    
    get_ajax_options(col_index,passport_full_path);
    
  });


  // var file_path1 = "<?php //echo "$passport_path" ?>";
  // //alert("file1: "+file_path1);
  //
  // $('#sel1').change(function() {
  //
  //   var column_name = $('#sel1').val();
  //   var passport_full_path = $('#sel1').attr("name");
  //
  //   var col_index = $(this).find('option:selected').attr("name");
  //   //var gff_file = "<?php //echo "$root_path"."/"."$downloads_path"."/vcf/Car.genes.gff.zip"; ?>";
  //   //alert("Selected: "+column_name+" "+col_index+" "+file_path1);
  //   //alert("file2: "+file_path1+", "+passport_dir);
  //
  //   get_ajax_options(col_index,passport_full_path);
  //
  // });





  //check input gene before sending form

  $('#egdb_search_file_form').submit(function() {
    var gene_id = $('#search_file_box').val();
    var data_set_selected = false;
    var file_database = "<?php echo $file_database; ?>";

    $('.sample_checkbox').each(function() {
      if ($(this).is(':checked')) {
        data_set_selected = true;
        return false;
      }
    });

    // Forms
    if (!gene_id) {
      $("#search_input_modal").html( "No input provided in the search box" );
      $('#no_gene_modal').modal();
      return false;
    }
    else if (gene_id.length < 3) {
      $("#search_input_modal").html( "Input is too short, please provide a longer term to search" );
      $('#no_gene_modal').modal();
      return false;
    }
    else if (file_database === '1' && !data_set_selected) {
      $("#search_input_modal").html( "No annotation file/s selected" );
      $('#no_gene_modal').modal();
      return false;
    }
    else {
      return true;
    };
  });
  
});

</script>