<!-- HEADER -->
<?php include_once realpath("../../header.php");
      include_once realpath("$easy_gdb_path/tools/common_functions.php");
      include_once realpath("../modal.html");
?>

<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

<div class="margin-20">
  <a class="float-right" href="#" target="blank"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<br>
<h2 style="text-align: center;"> SNP Extraction </h2>
<?php

// ------------------------------ GET VCF DATASETS FROM JSON FILE ------------------------------------------------
if (file_exists($json_files_path."/tools/vcf.json")) {
    $vcf_json_file = file_get_contents($json_files_path."/tools/vcf.json");
    $vcf_hash = json_decode($vcf_json_file, true);
    $json_exists = true;
    $vcf_dir_array= array_keys($vcf_hash);

      $all_datasets = get_dir_and_files($vcf_path); // call the function for get dirs and files

      $is_dir = false;
      $first_dir=true;

      sort($all_datasets);

      foreach ($all_datasets as $vcf_dataset) {
        
        if (is_dir($vcf_path."/".$vcf_dataset) && in_array($vcf_dataset,$vcf_dir_array)){ // get dirs in the folder vcf and json file and print categories
          $is_dir=true;
        }
      }
  }else{
    echo "<div class=\"alert alert-danger\" role=\"alert\" style=\"text-align:center; margin-top:10px\"> <b> vcf.json not found</b></div>";
    $json_exists = false;
  }

echo'<div id="container" class ="form margin-20" style="margin:auto; max-width:900px">';

?>


  <div class="form-group" >
    <label for="acc_list">Paste a list of ACC IDs</label>
    <textarea type="search_box" class="form-control" id="acc_list_box" rows="5" style="border-color: #666"></textarea>
  </div>

<?php
  if($is_dir)
  {

    echo '<label for="dataset_select">Select dataset</label>
    <select class="form-control form-control-lg" id="dataset_select" name="vcf_dataset">';

    foreach ($all_datasets as $vcf_dataset) {
      if ((is_dir($vcf_path."/".$vcf_dataset)) && in_array($vcf_dataset,$vcf_dir_array)){ // get dirs and print categories

        // echo $vcf_dataset. " | ". in_array($vcf_dataset,$vcf_dir_array);
        $data_set_name = preg_replace('/\.[a-z]{3}$/',"",$vcf_dataset);
        $data_set_name = str_replace("_"," ",$data_set_name);

        echo "<option value=\"$vcf_dataset\">$data_set_name</option>";

        if($first_dir)
        {
          $vcf_path_file= "$vcf_path/$vcf_dataset";
          $first_folder= $vcf_dataset;
          $first_dir=false;
        }
      }
  } 
  echo "</select>";
}
 else 
 {
    $vcf_path_file="$vcf_path";
    $first_folder= "";
 }

// echo $vcf_path_file;
// ------------------------------------------------------------------------------

// gene input form with gff and jbrowse
include_once realpath("gff_and_jbrowse.php");
?>

<form id="egdb_vcf_form" action="vcf_extract_acc_list_output.php" method="get">
  <div class="form-group" style="margin:30px !important">
    <label for="search_box">Select a genomic region</label> 
    <!-- <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button> -->

      <div class="input-group mt-3 mb-3" style="margin-top:0px !important">
        <div class="input-group-prepend">
          <select class="form-control form-control-lg" id="chr_select" name="vcf_chr">
            
            <?php
            foreach ($chr_file_array as $chr => $chr_file) {
              echo "<option value=\"$chr\">$chr</option>";
            }
            ?>
            
          </select>
        </div>
        <input id="vcf_input_start" type="text" class="form-control form-control-lg" placeholder="region start" name="vcf_start">
        <input id="vcf_input_end" type="text" class="form-control form-control-lg" placeholder="region end" name="vcf_end">
        <input type=hidden class="vcf_dataset_file form-control form-control-lg"  name="snp_file">
        <input type=hidden class="acc_list_select form-control form-control-lg"  name="acc_list">

        <button type="submit" class="btn btn-info float-right">Search</button>
      </div>
      
  </div>

</form>
  <hr>

<form id="egdb_vcf_id_form" action="vcf_id_extract_acc_list_output.php" method="get">
  <div class="form-group" style="margin:30px !important">
    <label for="vcf_snip_id">Type a SNP ID</label> 
    <!-- <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button> -->

      <div class="input-group mt-3 mb-3" style="margin-top:0px !important">
        <input id="vcf_snip_id" type="text" class="form-control form-control-lg" placeholder="SNP ID" name="snp_id">
        <input type=hidden class="vcf_dataset_file form-control form-control-lg"  name="snp_file">
        <input type=hidden class="acc_list_select form-control form-control-lg"  name="acc_list">
        <button type="submit" class="btn btn-info float-right">Search</button>
      </div>
      
  </div>
</form>

</div>

<!-- FOOTER -->
<?php include_once realpath("$easy_gdb_path/footer.php");?>

<script> 

$(document).ready(function () {
    
  var json_files_path = "<?php echo $json_files_path; ?>";
  var vcf_path = "<?php echo $vcf_path; ?>";
  var vcf_path_file= "<?php echo $vcf_path_file; ?>";
  var json_exists = "<?php echo json_encode($json_exists); ?>";
  var first_folder= "<?php echo $first_folder; ?>";
  var chr_select = [];

  if(json_exists == "false") { // if json file not exist hide the container
    $("#container").hide();
  }else{
      update_json_info_ajax_call(json_files_path,first_folder,vcf_path);
  }
  

function update_json_info_ajax_call(json_files_path,vcf_dir,vcf_path) {

  jQuery.ajax({
    type: 'POST',
    url: 'ajax_update_json_info.php',
    data: {'json_files_path': json_files_path, 'vcf_dir': vcf_dir, 'vcf_path': vcf_path},
    success: function(data) {
      var json_info = JSON.parse(data);

      gff_file = json_info.gff_file;
      jb_dataset = json_info.jb_dataset;
      names = json_info.genes_array;
      $("#chr_select").html(json_info.chr_select);
    }
  });
}
  //--------------------------------------------- 

  //----------------select dataset---------------

  $(document).ready(function() {
    var vcf_dataset = $('#dataset_select').val();
    $(".vcf_dataset_file").attr("value",vcf_dataset);
  })

  $('#dataset_select').change(function() {
    vcf_dataset = $('#dataset_select').val();
    $(".vcf_dataset_file").attr("value",vcf_dataset);

    var vcf_dataset_path = vcf_path+"/"+vcf_dataset;
    update_json_info_ajax_call(json_files_path,vcf_dataset,vcf_path);
   
  })
// --------------------------------------------- 


  //check input before sending form
  $('#egdb_vcf_form').submit(function() {
    var vcf_start = $('#vcf_input_start').val();
    var vcf_end = $('#vcf_input_end').val();
    var acc_list = $("#acc_list_box").val();

    $(".acc_list_select").val(acc_list);


    if (!vcf_start || !vcf_end || !acc_list) {
      if(!acc_list){
        $("#search_input_modal").html( "No input provided in the ACC list" );
        $('#no_gene_modal').modal();
      }else{
        $("#search_input_modal").html( "No input provided in the region search coordinates" );
        $('#no_gene_modal').modal();
      }
      return false;
    }
    else {
      return true;
    }
  });

    $('#egdb_vcf_id_form').submit(function() {
    var snip_id = $('#vcf_snip_id').val();
    var acc_list = $("#acc_list_box").val();

    $(".acc_list_select").val(acc_list);

    if (snip_id === "" || !acc_list) {
      if(!acc_list){
        $("#search_input_modal").html( "No input provided in the ACC list" );
        $('#no_gene_modal').modal();
      }else{      
      $("#search_input_modal").html( "No input provided in the SNP ID" );
      $('#no_gene_modal').modal();
    }
      return false;
    }
    else {
      return true;
    }
  });

});
</script>

