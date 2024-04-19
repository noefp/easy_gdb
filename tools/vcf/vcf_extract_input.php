<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<br>
<br>


<?php

$gene_names_file = "$root_path"."/"."$downloads_path"."/vcf/gene_names.txt";
$genes_array = [];

if ( file_exists($gene_names_file) ) {
  $tab_file = file($gene_names_file);

  //gets each replicate value for each gene
  foreach ($tab_file as $line) {
    $gene_name = trim($line);

    array_push($genes_array,$gene_name);
  }
}
  
?>

<!-- FORMULARIOS -->
<div style="margin:auto; max-width:1000px">

  <div class="form-group">
    <label for="usr">Find your gene coordinates</label>

    <div class="input-group mb-3">
      <input id="autocomplete_gene" type="text" class="form-control form-control-lg" placeholder="gene name">
      <div class="input-group-append">
        <button id="get_gene_coords" class="btn btn-success"><i class="fas fa-angle-double-down" style="font-size:28px;color:white;width:50px"></i></button>
      </div>
    </div>

  </div>

  <div id="jbrowse_frame">
  </div>
  <br>

  <div id="gff_html_card" class="card bg-light text-dark" style="display: none">
    <div class="card-body">
      <table class="table table-bordered" style="line-height: 1; font-size:14px">
        <thead><tr><th>Chr</th><th>feature</th><th>start</th><th>end</th><th>strand</th><th>info</th></tr></thead>
        <tbody id="gff_html_res"></tbody>
      </table>
      
    </div>
  </div><br>

  <br>




<form id="egdb_vcf_form" action="vcf_extract_output.php" method="get">
  <div class="form-group">
    <label for="search_box">Select a genomic region</label> 
    <!-- <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button> -->

      <div class="input-group mt-3 mb-3" style="margin-top:0px !important">
        <div class="input-group-prepend">
          <select class="form-control form-control-lg" id="chr_select" name="vcf_chr">
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
        <input id="vcf_input_start" type="text" class="form-control form-control-lg" placeholder="region start" name="vcf_start">
        <input id="vcf_input_end" type="text" class="form-control form-control-lg" placeholder="region end" name="vcf_end">
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
    
  var names = <?php echo json_encode($genes_array) ?>;
  //alert("hi: "+names[0])
    
  $( "#autocomplete_gene" ).autocomplete({
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(names, request.term);
      response(results.slice(0, 10));
    }
  });
  
  
  function get_gff_ajax_call(query_gene,gff_file) {
    //alert("Car.genes.gff2: "+query_gene+", "+gff_file);
    
    jQuery.ajax({
      type: "POST",
      url: 'ajax_get_gene_coordinates.php',
      data: {'query_gene': query_gene,'gff_file': gff_file},

      success: function (gff_array) {
        
        var gff_lines = JSON.parse(gff_array);
        
        //alert("Car.genes.gff3: "+gff_array);
        
        $("#gff_html_res").html(gff_lines.join("<br>"));
        $("#gff_html_card").css("display","block");
        // var table_width = $("#gff_html_res").width() + 30;
        // $("#gff_html_card").css("width",table_width+"px");
        
        
        var jb_dataset = "easy_gdb_sample";
        //var jb_gene_name = "gene1.1";
        var jb_gene_name = query_gene;
        
        
        $("#jbrowse_frame").html("<a class=\"float-right jbrowse_link\" href=\"/jbrowse/?data=data%2F"+jb_dataset+"&loc="+jb_gene_name+"&tracks=DNA%2Ctranscripts&highlight=\">Full screen</a><iframe class=\"jb_iframe\" src=\"/jbrowse/?data=data%2F"+jb_dataset+"&loc="+jb_gene_name+"&tracks=DNA%2Ctranscripts&highlight=\" name=\"jbrowse_iframe\"><p>Your browser does not support iframes.</p> </iframe>");
        
        
      }
    });
    
  }; // end ajax_call
  
  
  
  $('#get_gene_coords').click(function() {
    
    var query_gene = $('#autocomplete_gene').val();
    var gff_file = "<?php echo "$root_path"."/"."$downloads_path"."/vcf/Car.genes.gff.zip"; ?>";
    //alert("Car.genes.gff: "+query_gene+", "+gff_file);
    
    get_gff_ajax_call(query_gene,gff_file);
    
  });

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

<style>
  .jb_iframe {
    border: 1px solid rgb(80, 80, 80);
    height: 300px;
    width: 100%;
    margin-right: 20px;
  }
</style>
