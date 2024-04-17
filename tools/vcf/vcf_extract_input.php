<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<br>
<br>

<!-- FORMULARIO -->
<div style="margin:auto; max-width:900px">

<form id="egdb_vcf_form" action="vcf_extract_output.php" method="get">
  <div class="form-group">
    <label for="search_box" style="font-size:16px">Select a genomic region</label> 
    <!-- <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button> -->

      <div class="input-group mt-3 mb-3" style="margin-top:0px !important">
        <div class="input-group-prepend">
          <select class="form-control" id="chr_select" name="vcf_chr">
            <option value='chr1' selected>chr1</option>
            <option value='chr2'>chr2</option>
            <option value='chr3'>chr3</option>
            <option value='chr4'>chr4</option>
            <option value='chr5'>chr5</option>
            <option value='chr6'>chr6</option>
            <option value='chr7'>chr7</option>
            <option value='chr8'>chr8</option>
          </select>
        </div>
        <input id="vcf_input_start" type="text" class="form-control" placeholder="region start" name="vcf_start">
        <input id="vcf_input_end" type="text" class="form-control" placeholder="region end" name="vcf_end">
        <button type="submit" class="btn btn-info float-right">Extract</button>
      </div>
      
  </div>

  <br>
  <br>
  <br>
</form>
</div>


<!-- CARTELITO DE ERROR -->
<div class="modal fade" id="vcf_error_modal" role="dialog">
  <div class="modal-dialog modal-sm">

    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" style="text-align: center;">ERROR</h4>
      </div>
      <div class="modal-body">
        <div style="text-align: center;">
          <p id="error_p_modal"></p>
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

<script> 

$(document).ready(function () {

  //check input before sending form
  $('#egdb_vcf_form').submit(function() {
    var vcf_start = $('#vcf_input_start').val();
    var vcf_end = $('#vcf_input_end').val();
    
    if (!vcf_start || !vcf_end) {
      $("#error_p_modal").html( "No input provided in the region search coordinates" );
      $('#vcf_error_modal').modal();
      return false;
    }
    else {
      return true;
    }
  });

});
</script>
