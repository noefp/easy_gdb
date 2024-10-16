<!-- HEADER -->
<?php 
  include_once realpath("../../header.php");
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
?>

<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


<!-- HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php"><i class="fa fa-info" style="font-size:20px;color:#229dff"></i> Help</a>
</div>

<a href="/easy_gdb/index.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>

<br>
<h3 class="text-center">Passport Search</h3>



<?php
  $n_passport_files=[];


  function read_passport_file($passport_path,$passport_file,$acc_header_name) {
    
    
    $dataset_name = preg_replace('/\.[a-z]{3}$/',"",$passport_file);
    $dataset_name = str_replace("_"," ",$dataset_name);
    $frame_id = preg_replace('/[. ]txt/',"",$passport_file);
    
    // $link_name = preg_replace('/\s|\.|\d/', '', $dataset_name);
    
    // echo "<h4>$passport_path/$passport_file</h4>";
    // echo "<h4>$frame_id</h4>";
    
    if (file_exists("$passport_path/$passport_file")) {
         
      echo "<div class=\"collapse_section pointer_cursor\" data-toggle=\"collapse\" data-target=\"#collapse_$frame_id\" aria-expanded=\"true\">";
        echo "<i class=\"fas fa-sort\" style=\"color:#229dff\"></i> $dataset_name";
      echo "</div>";

      echo "<div id=\"collapse_$frame_id\" class=\"hide collapse\" style=\" border-radius: 5px; border:solid 1px; background-color:#efefef; padding-top:7px\">";
      array_push($GLOBALS['n_passport_files'],$frame_id);
      
      $pass_array = file("$passport_path/$passport_file");
      // $pass_array = explode("\n", $pass_array);
      $header = array_shift($pass_array);
      $header_array = explode("\t", $header);
      
      // echo "passport header: $header";
      
      $no_spc_file = str_replace(" ","\ ","$passport_path/$passport_file");
      
      // foreach ($header_array as $key => $value) {   
      //   $col_index = $key + 1; 
      //   $shell_cmd = "tail -n +2 $no_spc_file | cut -f $col_index | sort -u";
      //   $shell_res = shell_exec($shell_cmd);
      //   $is_numeric = 1;
      //   // echo "hello!!! $shell_res <br>";
      //   if ( preg_match("/[A-Za-z]/", $shell_res) ) {
      //     $is_numeric = 0;
      //   }
      //   $shell_array = explode("\n",$shell_res);
      //   $shell_res = "";
      

      echo "<form id=\"passport_form\" action=\"passport_search_output_avanced.php\" method=\"post\">";
        echo "<div class=\"container\" style=\"margin-left:20px\">";
            echo "<div class=\"row\">";
              echo "<div class=\"col\">";
                echo "<label for=\"select_$frame_id\" style=\"margin-left:15px;margin-right:15px \"><i>Filter by: </i></label>";
                echo "<select class=\"form-control sel_opt\" id=\"$frame_id\" name=\"$no_spc_file\" style=\"width:auto; display: inline-block;\">";
                // echo "<option selected></option>";
                foreach ($header_array as $index => $value) {
                  // if ($value != $acc_header_name) {
                    echo "<option name=\"$index\">$value</option>";
                  // }
                }
                echo "</select>";

             echo "</div>";
            echo "<div class=\"col\">";
              echo "<label for=\"text_$frame_id\"  style=\"margin-left:45px;margin-top:10px\"><i>Added filter:</i></label>";
            echo "</div>";
            echo "</div>";

            echo "<div class=\"d-flex\" style=\"display: inline-block;margin:10px\">";
              echo "<select multiple id=\"select_$frame_id\" size=\"11\" class=\"form-control select\"></select>";
              echo "<input id=\"numeric_input_$frame_id\" type=\"number\" class=\"form-control\" name=\"\" style=\"height:50px;display:none; background-color:#ffff; margin-left: 20px\" placeholder=\"0\">";
        
              echo "<div id=\"button_$frame_id\" style=\"margin:10px;margin-top:60px;width:20%; text-align: center\">";
              echo "<button class=\"btn btn-success add\" style=\"width:90%;height:20%;font-size:small\">Add <span class=\"fas fa-angle-double-right\"></span></button><br>";
              echo "<button class=\"btn btn-danger delete\" style=\"margin-top:40px; width:90%;height:20%; font-size:small\"><span class=\"fas fa-angle-double-left\"></span> Quit</button>";
              echo "</div>";

            echo "<textarea id=\"text_$frame_id\" class=\"form-control\" name=\"filters\" rows=\"10\" cols=\"5\" readonly=\"true\" wrap=\"hard\" style=\"background-color:#ffff;resize: none\"></textarea>"; 
            echo "</div>"; // col
            echo "</div>";

        echo"<div style=\"display: flex; justify-content: flex-end;\">";
        echo "<button id=\"search_$frame_id\" name=\"file\" value=\"$frame_id\" type=\"submit\" class=\"btn btn-info search_button\" style=\"margin:10px; width:95px\"><span class=\"fas fa-search\"></span> Search</button>";
        echo"</div>";
        echo "</div>";
     echo "</form>"; 
    } // if file exist
  }
?>


<!-- INPUT FORM -->

<!-- Default filter    -------------------------------   -->
<div class="form margin-20">
  <div style="margin:auto; max-width:1200px">

      <!-- FORM OPPENED -->
    <form id="egdb_passport_form" action="passport_search_output.php" method="get">
      <div class="form-group">
        <label for="search_box" style="font-size:16px">Insert an accession ID or passport keywords</label>
        <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button>
        <input id="search_box" type="search_box" class="form-control" name="search_keywords" style="border-color: #666">
      </div>
      <br>
      <button type="submit" class="btn btn-info float-right" style="margin-top: -5px">Search</button>
      <br>
      <br>
      <br>
    </form>
<!--  ------------------------------------------------------------->
<br>


<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#advanced" aria-expanded="true" style="text-align:center">
  <i class="fas fa-sort" style="color:#229dff;"></i> <h3 style="display:flex inline"> Advanced Search </h3> <i for="collapse_section" class="fas fa-sort" style="color:#229dff"></i>
</div>

<div id="advanced" class="hide collapse">

<?php
//
  $all_datasets = get_dir_and_files($passport_path); // find dirs in passport path
  asort($all_datasets);

 $dir_counter = 0;

 foreach ($all_datasets as $one_dir) {
   if (is_dir($passport_path."/".$one_dir)){ // get dirs and print categories
     $dir_counter++;
   }
 }


  //category organization
 if ($dir_counter) {
//
    foreach ($all_datasets as $dir_or_file) {
      if (is_dir($passport_path."/".$dir_or_file)){ // get dirs and print categories

        $dir_name = str_replace("_"," ",$dir_or_file);  
//
        $pass_files = get_dir_and_files($passport_path."/".$dir_or_file); // call the function
        sort($pass_files);

//
      //  foreach ($pass_files as $passport_file) {
//
          // echo "passport_path: $passport_path/$dir_or_file/passport.json<br>";
//
          // get info from passport.json
          if ( file_exists("$passport_path/$dir_or_file/passport.json") ) {
            $pass_json_file = file_get_contents("$passport_path/$dir_or_file/passport.json");
            $pass_hash = json_decode($pass_json_file, true);

            $passport_file = $pass_hash["passport_file"];
            $phenotype_file_array = $pass_hash["phenotype_files"];
            $unique_link = $pass_hash["acc_link"];



          if ( !preg_match('/\.php$/i', $passport_file) && !is_dir($passport_path.'/'.$dir_or_file.'/'.$passport_file) &&  !preg_match('/\.json$/i', $passport_file) && file_exists($passport_path.'/'.$dir_or_file.'/'.$passport_file)   ) {
            
            // echo "unique_link: $unique_link<br>";
//
            read_passport_file("$passport_path/$dir_or_file",$passport_file,$unique_link);

            foreach ($phenotype_file_array as $phenotype_file) {
              read_passport_file("$passport_path/$dir_or_file",$phenotype_file,$unique_link);
            }
//
           }//if preg_match
         }//foreach all_dir
      }//if is_dir
    }// foreach dir
}//if dir_counter
  else {
//     // without categories
   read_passport_file($passport_path,$passport_file,$unique_link);
 }
?>
  </div>
</div>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>





<!-- ERROR BANNER -->
<div class="modal fade" id="no_gene_modal" tabindex="-1" aria-labelledby="genesNotFoundLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog">
  <!-- <div class="modal-dialog modal-sm"> -->
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title w-100 text-center" id="genesEmpty" style="color: red">❌ <b>Error</b></h1>
      </div>
      <div class="modal-body">
        <div style="text-align: center;">
          <p id="search_input_modal"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- -------------------------------------------------------------------------------------------------------------- -->

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

  .collapse_section{
/*  text-decoration: underline;*/
  background-color:white;
  color:black;
  border-radius: 5px;
  }  

  .collapse_section:hover  {
/*  text-decoration: underline;*/
  background-color: #6c757d;
  color:#fff;
}

</style>


<!-- JAVASCRIPT -->
<script> 
$(document).ready(function () {


  function get_ajax_options(col_index,query_file,filter_id) {
    // alert("file: "+query_file+", col_index: "+col_index);

    jQuery.ajax({
      type: "POST",
      url: 'passport_ajax.php',
      data: {'col_index': col_index, 'query_file': query_file},

      success: function (opt_array) {
        // alert("opt_lines: "+opt_array);

        var opt_lines = JSON.parse(opt_array);
        // alert("opt_lines: "+opt_lines[0]);
        $("#select_" + filter_id).html(opt_lines.join("<br>"));

        if (opt_lines[0] == '<option name=\"gt\">></option>') {
          // alert("opt_lines: "+opt_lines[0]);

          $('#' + 'numeric_input_' + filter_id).css("display","inline");
          $('#' + 'numeric_input_' + filter_id).css("width","80%");
          $('#'+'select_' + filter_id).css("width",100+"px");
          $('#'+'select_' + filter_id).css("height",50+"px");   
          $('#'+'select_' + filter_id).removeAttr("multiple");
          $('#'+'select_' + filter_id).removeAttr("size");

        } else {
          $('#' + 'numeric_input_' + filter_id).css("display","none");
          $('#'+'select_' + filter_id).css("width","100%");
          $('#'+'select_' + filter_id).css("height","100%");
          $('#'+'select_' + filter_id).attr("multiple","multiple");
          $('#'+'select_' + filter_id).attr("size","11");
        }

      }
    });
  }; // end ajax_call
  
  $('.sel_opt').before(function() {

    var filter_id=this.id;
    var Selec = $('#' + filter_id).val();
    
    var passport_full_path = $('#' + filter_id).attr("name");

    var col_index = $(this).find('option:selected').attr("name"); 
    
    get_ajax_options(col_index,passport_full_path,filter_id);

  });

  $('.sel_opt').change(function() {
    
    var filter_id=this.id;
    var Selec = $('#' + filter_id).val();
    
    var passport_full_path = $('#' + filter_id).attr("name");

    var col_index = $(this).find('option:selected').attr("name"); 
    
    get_ajax_options(col_index,passport_full_path,filter_id);
    
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



  var files=<?php echo (json_encode($n_passport_files));?>;
  var all_filters=[];

  $('.select').dblclick(function() {
    var attr_id=$(this).attr('id');
    var id = attr_id.replace("select_","");
    add(id);

  });


$('.add').click(function() {

  event.preventDefault();

  var parent_id=$(this).parent().attr('id');
  var id = parent_id.replace("button_","");
  add(id);

});

function add (id){
  if(!all_filters[id])
  {
    all_filters[id]=[];
  }

  var category_select = $('#' + id).val();
  // var filter_select=$('#select_' + id).val().join('\n');

  var filter_select=$('#select_' + id).val();
  
  if(category_select=="" || filter_select=="")
    {  
      var text_error_id=document.getElementById('search_input_modal');
      text_error_id.textContent = "Categories or Filter Not Selected";
      var myModal = new bootstrap.Modal(document.getElementById('no_gene_modal'), {
        keyboard: false
        });
        myModal.show()
        return false;
    }else
    {
      if(/^[<>=]/.test(filter_select)) {
        if($('#numeric_input_' + id).val()=="")
        {
          filter_select=filter_select +" "+ 0;
        }
        else{
          filter_select=filter_select + " " + $('#numeric_input_' + id).val();
        }

        const newFilter = {
          category: category_select,
          filter: filter_select
        };

        //add a new filter to diccionaire
        all_filters[id].push(newFilter);
      }
      else{

      filter_select=$('#select_' + id).val().join('\n')

      filter_select.split("\n").forEach(n_filter=>{
      //create a dicctionaire
        const newFilter = {
          category: category_select,
          filter: n_filter
        };

        //add a new filter to diccionaire
        all_filters[id].push(newFilter);
        });
      }

       // clean the textarea
       $('#text_' + id).val('');

       all_filters[id].forEach(filters => {
          $('#text_' + id).val($('#text_' + id).val() + `${filters.category} -> ${filters.filter}\n`);
      });
    }
  
}

$('.delete').click(function() {

  event.preventDefault();
  
  var parent_id=$(this).parent().attr('id');
  var id = parent_id.replace("button_","");

  var filters = $('#text_' + id).val();

  if(filters=="")
    {  
      var text_error_id=document.getElementById('search_input_modal');
      text_error_id.textContent = "Filters empty";
      var myModal = new bootstrap.Modal(document.getElementById('no_gene_modal'), {
        keyboard: false
        });
        myModal.show()
        return false;
    }else
    {
      // delete  the last filter to diccionaire
      all_filters[id].pop();

      // clean the textarea
      $('#text_' + id).val('');

      all_filters[id].forEach(filter => {
      $('#text_' + id).val($('#text_' + id).val() + `${filter.category} -> ${filter.filter}\n`);
      });

    }


});

$('.search_button').click(function() {

  var parent_id=$(this).attr('id');
  var id = parent_id.replace("search_","");

});

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
      $('#no_gene_modal').modal('show');
      return false;
    }
    else {
      return true;
    };
  });
  
});

</script>

