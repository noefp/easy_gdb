
<!-- HEADER -->
<?php 
  include_once realpath("../../header.php");
  include_once realpath("$easy_gdb_path/tools/common_functions.php");
?>

<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<!-- Bootstrap<script <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->

<!-- HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/01_search.php" target="blank"><i class="fa fa-info" style="font-size:20px;color:#229dff"></i> Help</a>
</div>

<!-- <a href="/easy_gdb/index.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br> -->


<!---------------------------------------------- FRONT MAIN ------------------------------------------------------------------------------------>
<br>
<h3 class="text-center">Passport Search</h3>


<!--------------------------------- Default filter    --------------------------------->
<div class="form margin-20" >
  <div style="margin:auto; max-width:1200px">

      <!-- FORM OPPENED -->
    <form id="egdb_passport_form" action="passport_search_output.php" method="get">
      <div class="form-group">
        <label for="search_box" style="font-size:16px">Insert an accession ID or passport keywords</label>
        <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button>
        <input id="search_box_default" type="search_box" class="form-control" name="search_keywords" style="border-color: #666">
      </div>
      <br>
      <button id="search_buttom_default" type="submit" class="btn btn-info float-right" style="margin-top: -15px">Search</button>
      <br>


      <?php
        $all_datasets = get_dir_and_files($passport_path); // call the function
        $is_dir=false;
        sort($all_datasets);
        
        if ($all_datasets) {
          foreach ($all_datasets as $expr_dataset) {
            if (is_dir($passport_path."/".$expr_dataset)){ // get dirs and print categories
                $is_dir=true;
                break;
              }
            }

        if($is_dir)
          {
            echo "<lable style=\"margin-left:5px\"><i>Select Dataset</i></lable>";
            echo "<div style=\"border:groove 1px;\">";

            foreach ($all_datasets as $expr_dataset) {
              if (is_dir($passport_path."/".$expr_dataset)){ // get dirs and print categories

                $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$expr_dataset);
                $data_set_name = str_replace("_"," ",$data_set_name);
              }

            if ( is_dir("$passport_path/$expr_dataset") && file_exists("$passport_path/$expr_dataset") ) {
            {
                echo "<div style=\"margin-left:45px; display:inline-flex; width:180px\">";
                echo "<label><input type=\"checkbox\" class=\"form-check-input\" id=\"$expr_dataset\" name=\"checkboxes[]\" value=\"$expr_dataset\"><a style=\"color:black\" class=\"pointer_cursor\">$data_set_name</a></label>";
                echo"</div>";     
            }
          }
        }
        echo "</div>";
      }
    }
    ?>
    </form>
<!--  ------------------------------------------------------------->
<br>

<!-------------------------------------------  Filter avanced main -------------------------------------------------->
<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#advanced" aria-expanded="true" style="text-align:center">
  <i class="fas fa-sort" style="color:#229dff;"></i> <h3 style="display:flex inline"> Advanced Search </h3> <i for="collapse_section" class="fas fa-sort" style="color:#229dff"></i>
</div>

<div id="advanced" class="hide collapse">

<?php
//
$files_phenotype_count=0;

$all_datasets = get_dir_and_files($passport_path); // call the function
asort($all_datasets);

$dir_counter = 0;
$dir_counter2=true;

foreach ($all_datasets as $expr_dataset) {
  
  if (is_dir($passport_path."/".$expr_dataset)){ // get dirs and print categories
    $dir_counter++;
  }
}


//category organization
if ($dir_counter) {
  
  echo "<label style=\"margin:3px\" for=\"sel1\"><i>Select Dataset</i></label>";
  get_info_json($passport_path);
  echo "<select class=\"form-control\" id=\"sel1\" name=\"expr_file\">";
  
  //get expression datasets from each dir
  foreach ($all_datasets as $one_dataset) {
    $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$one_dataset);
    $data_set_name = str_replace("_"," ",$data_set_name);
    
    // if ( !preg_match('/\.php$/i', $one_dataset) && is_dir("$passport_path/$one_dataset") && !preg_match('/\.json$/i', $one_dataset) && file_exists("$passport_path/$one_dataset") ) {
      if (is_dir("$passport_path/$one_dataset") && file_exists("$passport_path/$one_dataset") ) {

      echo "<option value=\"$passport_path/$one_dataset\">$data_set_name</option>";
      if($dir_counter2)
      {
        $first_category= "$passport_path/$one_dataset";
        $dir_counter2=false;
      }
    }
  }    
  echo   "</select>";

  echo "<div id=\"frame\">";
    get_info_json($first_category);
  echo"</div>";

}
 else 
 {
    get_info_json($passport_path);
 }

?>
</div>
</div>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>

<!------------------------------------------------- END MAIN---------------------------------------------------- -->

<!-- ............................................................................................................ -->
<!--------------------------------- Functions ---------------------------------------------------------------------->

<?php

 // get info from passport.json
 function get_info_json($passport_path_file)
  {
    if ( file_exists($passport_path_file."/passport.json") ) {
    $pass_json_file = file_get_contents($passport_path_file."/passport.json");
    $pass_hash = json_decode($pass_json_file, true);
    // print_r($pass_hash);

    $passport_file = $pass_hash["passport_file"];
    $phenotype_file_array = $pass_hash["phenotype_files"];
    $counts_phenotypes=0;
    $files_phenotypes_exist=[];
    // $unique_link = $pass_hash["acc_link"];



  if ( !preg_match('/\.php$/i', $passport_file) && !is_dir($passport_path_file.'/'.$passport_file) &&  !preg_match('/\.json$/i', $passport_file) && file_exists($passport_path_file.'/'.$passport_file)   ) {
    
    // echo "unique_link: $unique_link<br>";
  echo'<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#passport_search" aria-expanded="true" style="text-align:left;"> <i class="fa fa-list" style="color:#229dff;"></i> <h3 style="display:flex inline"> Passport search </h3>
    </div>
    <div id="passport_search" class="show collapse">';
      read_passport_file($passport_path_file,$passport_file,"passport");
    echo "</div>";
  }

  if (!empty($phenotype_file_array)){

    foreach ($phenotype_file_array as $index =>$phenotype_file) {
      if ( !preg_match('/\.php$/i', $phenotype_file) && !is_dir($passport_path_file.'/'.$phenotype_file) &&  !preg_match('/\.json$/i', $phenotype_file) && file_exists($passport_path_file.'/'.$phenotype_file)) {
        $counts_phenotypes++;
        array_push($files_phenotypes_exist,$phenotype_file);
      }
    }
    if ($counts_phenotypes){

      echo '<div class="collapse_section pointer_cursor" data-toggle="collapse" data-target="#phenotype_search" aria-expanded="true" style="text-align:left;">
      <i class="fa fa-list" style="color:#229dff;"></i> <h3 style="display:flex inline"> Phenotype search </h3>
      </div>
      <div id="phenotype_search" class="show collapse"">';

      if ($counts_phenotypes>1){
        $GLOBALS['files_phenotype_count']=$counts_phenotypes;
      foreach($files_phenotypes_exist as $index => $phenotype_file){
          read_passport_file($passport_path_file,$phenotype_file,"phenotype".($index+1));}

        echo'<div class="all_phenotype_search" style="display: flex; justify-content: flex-end;">
        <button  id="submit_all_forms"class="btn btn-info search_button" type="submit" style="margin:20px;"><span class="fas fa-search"></span> All Phenotype Search</button>
        </div>';
        }else{
          read_passport_file($passport_path_file,$phenotype_file_array[0],"phenotype");
        }
        echo "</div>";
        }// if no counts
  }// if empty
    }
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  // $n_passport_files=[];
  function read_passport_file($passport_path,$passport_file,$form_id) {
    
    
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

      echo "<div id=\"collapse_$frame_id\" class=\"hide collapse\" style=\" border-radius: 5px; border:groove 2px; background-color:#efefef; padding-top:7px\">";
      // array_push($GLOBALS['n_passport_files'],$frame_id);
      
      $pass_array = file("$passport_path/$passport_file");
      // $pass_array = explode("\n", $pass_array);
      $header = array_shift($pass_array);
      $header_array = explode("\t", $header);
      
      // echo "passport header: $header";
      
      $no_spc_file = str_replace(" ","\ ","$passport_path/$passport_file");

      echo "<form id=\"$form_id\" action=\"passport_search_output_avanced.php\" method=\"post\">";
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
              echo "<select multiple id=\"select_$frame_id\" size=\"5\" class=\"form-control select\"></select>";
              echo "<input id=\"numeric_input_$frame_id\" type=\"number\" class=\"form-control\" name=\"\" style=\"height:50px;display:none; background-color:#ffff; margin-left: 20px;\" placeholder=\"0\">";
        
              echo "<div id=\"button_$frame_id\" style=\"margin:10px;margin-top:20px;width:20%; text-align: center\">";
              echo "<button class=\"btn btn-success add\" style=\"width:90%;font-size:small\">Add <span class=\"fas fa-angle-double-right\"></span></button><br>";
              echo "<button class=\"btn btn-danger delete\" style=\"margin-top:20px; width:90%; font-size:small\"><span class=\"fas fa-angle-double-left\"></span> Quit</button>";
              echo "</div>";

            echo "<textarea id=\"text_$frame_id\" class=\"form-control\" name=\"filters\" rows=\"4\" cols=\"5\" readonly=\"true\" wrap=\"hard\" style=\"background-color:#ffff;resize:true\"></textarea>"; 
            echo "</div>"; // col
            echo "</div>";
            echo "<input name=\"passport\" value=\"$passport_path\" style=\"display:none\"/>";
            echo "<input name=\"file\" value=\"$frame_id\" style=\"display:none\"/>";

        echo"<div style=\"display: flex; justify-content: flex-end;\">";
        echo "<button id=\"search_$frame_id\" type=\"submit\" class=\"btn btn-info search_button\" style=\"margin:10px;\"><span class=\"fas fa-search\"></span> Search</button>";
        echo"</div>";
        echo "</div>";
     echo "</form>"; 
    } // if file exist
  }

?>

<!-- --------------------------------------------END funnctiond php----------------------------------------------------------------------------------------------------- -->

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
  /* background-color:white; */
  color:black;
  border-radius: 5px;
  margin-bottom: 0px;
  }  

  .collapse_section:hover  {
/*  text-decoration: underline;*/
  background-color: #6c757d;
  color:#fff;
}

#phenotype_search , #passport_search
{ 
  margin-left:20px;
  background-color:#efefef;
  border-radius: 0px 0px 5px 5px;
  padding-top: 1px;

}
</style>


<!--..... JAVASCRIPT.................. -->
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
        // alert("opt_lines: "+opt_lines);
        $("#select_" + filter_id).html(opt_lines.join("\n"));

        if (opt_lines[0] == '<option name=\"gt\">></option>') {
          // alert("opt_lines: "+opt_lines[0]);

          $('#' + 'numeric_input_' + filter_id).css("display","inline");
          $('#' + 'numeric_input_' + filter_id).css("width","80%");
          $('#'+'select_' + filter_id).css("width",100+"px");
          $('#'+'select_' + filter_id).css("height",50+"px");   
          $('#'+'select_' + filter_id).removeAttr("multiple");
          $('#'+'select_' + filter_id).removeAttr("size");
        } else{
            $('#' + 'numeric_input_' + filter_id).css("display","none");
            $('#'+'select_' + filter_id).css("width","100%");
            $('#'+'select_' + filter_id).css("height","100%");
            $('#'+'select_' + filter_id).attr("multiple","multiple");
            $('#'+'select_' + filter_id).attr("size","5");
        }
      }
    });
  }; // end ajax_call

  //call PHP file ajax_get_names_array.php to get the gene list to autocomplete from the selected dataset file
  function ajax_search_call(passport_path) {
  
  jQuery.ajax({
    type: "POST",
    url: 'ajax_search_avanced.php',
    data: {'passport_path': passport_path},

    success: function (passport_array) {
      // alert(passport_array);

      var passport_filter = JSON.parse(passport_array);
      // alert(passport_filter);
      counts_files=passport_filter[0];
      passport_filter.shift();


    $("#frame").html(passport_filter.join("\n"));

    $('.sel_opt').before(function(){  
    var filter_id=this.id;
    var Selec = $('#' + filter_id).val();  
    var passport_full_path = $('#' + filter_id).attr("name");
    var col_index = $(this).find('option:selected').attr("name");   
    get_ajax_options(col_index,passport_full_path,filter_id);
  });
    }
  });

};

// ...........................................................

$('.sel_opt').before(function(){  
    var filter_id=this.id;
    var Selec = $('#' + filter_id).val();  
    var passport_full_path = $('#' + filter_id).attr("name");
    var col_index = $(this).find('option:selected').attr("name");   
    // alert(filter_id);
    get_ajax_options(col_index,passport_full_path,filter_id);
  });


  // Get dataset genes when changing dataset
    $(document).on('change', '#sel1', function() {
      selected_dataset = $('#sel1').val();
      ajax_search_call(selected_dataset);
      // alert($('#sel1').innerHTML = $('#frame option:nth-child(1)').id); 

  });

  $(document).on('change', '.sel_opt', function() {
    var filter_id=this.id;
    // alert(filter_id)
    var Selec = $('#' + filter_id).val();   
    var passport_full_path = $('#' + filter_id).attr("name");
    var col_index = $(this).find('option:selected').attr("name"); 
    get_ajax_options(col_index,passport_full_path,filter_id);
    
  });

  var all_filters=[];

  $(document).on('dblclick', '.select', function() {
    var attr_id=$(this).attr('id');
    var id = attr_id.replace("select_","");
    add(id);

  });

  $(document).on('click', '.add', function() {
  event.preventDefault();
  var parent_id=$(this).parent().attr('id');
  var id = parent_id.replace("button_","");
  add(id);

});

//............. function that add a filter to the textarea and add to array of filters.....................
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

      filter_select=$('#select_' + id).val().join("\n")

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


//..... function that delete a filter to the textarea and delete to array of filters.........
$(document).on('click', '.delete', function() {

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

// $(document).on('click', '.search_button', function() {
//   var parent_id=$(this).attr('id');
//   var id = parent_id.replace("search_","");
// });


  // //check input gene before sending form
  $(document).on('submit', '#egdb_passport_form', function() 
  {
    var gene_id = $('#search_box_default').val();
    var data_set_selected = false;
    var files_database = "<?php echo json_encode($is_dir); ?>";

    if(files_database === 'true')
    { $('.form-check-input').each( function() {
        if ($(this).is(':checked')) {
          data_set_selected = true;
        }
      }); 
    }else{data_set_selected = true;}   

    //   // Forms validation
  if ((!data_set_selected) && (files_database === 'true')) {
      $("#search_input_modal").html( "No dataset file/s selected" );
      $('#no_gene_modal').modal('show');
      return false;
    } else if (!gene_id) {
      $("#search_input_modal").html( "No input provided in the search box" );
      $('#no_gene_modal').modal();
      return false;
    } else if (gene_id.length < 3) {
      $("#search_input_modal").html( "Input is too short, please provide a longer term to search" );
      $('#no_gene_modal').modal();
      return false;
    }
    else {
      return true;
    };
  }); 
});

// ...................all_phenotypes_filter...............................

var counts_files=<?php echo $files_phenotype_count; ?>;
var forms=[];
var filters=[];
$(document).on('click', '#submit_all_forms', function()
 {
  // alert(counts_files);
  // get the information from each phenotype form
  for(var index=1; index<=counts_files; index++)
  {
      var id = "phenotype"+index;
      forms[index]= new FormData(document.getElementById(id));
  }


// Create a temporary form
 var tempForm = document.createElement('form');
 tempForm.action = 'passport_search_phenotypes.php';
 tempForm.method = 'POST';
  tempForm.style.display = 'none';

// add elements
  var forms_counts = document.createElement('input');
  forms_counts.name = "counts";
  forms_counts.value = counts_files;
  tempForm.appendChild(forms_counts);

 forms.forEach((data ,index) => {
  data.forEach((value, key) => {
  var input = document.createElement('input');
  input.name = key+index;
  if(key=="filters")
  { 
    input.value = value.split("\n").join("\t");
  }
  else{
    input.value = value;
  }

  if(key=="passport")
  {
    input.name = key;
  }else{
    input.name = key+index;
  }
  
  tempForm.appendChild(input);
  });
 }); 

  // Add the temporary form to the document
document.body.appendChild(tempForm); 
tempForm.submit();
});

</script>

<!-- Modal popup erro message -->

<div class="modal fade" id="no_gene_modal" tabindex="-1" role="dialog" aria-labelledby="genesNotFoundLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title  w-100 text-center" id="genesNotFoundLabel">❌ Error</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"> -->
          <!-- <span aria-hidden="true">&times;</span> -->
        </button>
      </div>
      <div class="modal-body">
        <div style="text-align: center;">
          <p id="search_input_modal"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!-- -------------------------------------------------------------------------------------------------------------- -->