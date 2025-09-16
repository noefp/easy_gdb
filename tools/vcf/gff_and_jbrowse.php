<?php
echo '
  <div class="form-group"  style="margin:30px !important">
    <label for="autocomplete_gene">Find your gene coordinates</label>

    <div class="input-group mb-3" >
      <input id="autocomplete_gene" type="text" class="form-control form-control-lg" placeholder="gene name">
      <div class="input-group-append">
        <button id="get_gene_coords" class="btn btn-success"><i class="fas fa-angle-double-down" style="font-size:28px;color:white;width:50px"></i></button>
      </div>
    </div>
  </div>

<!-- <div id="jbrowse_frame_and_table" class="alert" style="display: none;"><button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close"> <span aria-hidden="true">&times;</span></button> -->
<div id="jbrowse_frame"></div>

  <div id="gff_html_card" class="card bg-light text-dark" style="display: none">
    <div class="card-body" style="overflow-x:auto;">
      <table class="table table-bordered" style="line-height: 1; font-size:14px">
        <thead><tr><th>Chr</th><th>feature</th><th>start</th><th>end</th><th>strand</th><th>info</th></tr></thead>
        <tbody id="gff_html_res"></tbody>
      </table>  
    </div>
  </div>
<!-- </div> --> 
' ;
?>

<script>
$(document).ready(function() {  
  $( "#autocomplete_gene" ).autocomplete({
    source: function(request, response) {
      var results = $.ui.autocomplete.filter(names, request.term);
      response(results.slice(0, 10));
    }
  });
  
  // ----------- Ajax call functions------------------------

  function get_gff_ajax_call(query_gene,gff_file,jb_dataset) {
    //alert("Car.genes.gff2: "+query_gene+", "+gff_file);
    
    jQuery.ajax({
      type: "POST",
      url: 'ajax_get_gene_coordinates.php',
      data: {'query_gene': query_gene,'gff_file': gff_file},

      success: function (gff_array) {
        
        var gff_lines = JSON.parse(gff_array);
        
        // alert("Car.genes.gff3: "+gff_array);
        

        $("#gff_html_res").html(gff_lines.join("<br>"));
        $("#gff_html_card").css("display","block");
        // var table_width = $("#gff_html_res").width() + 30;
        // $("#gff_html_card").css("width",table_width+"px");
        
      
        var jb_gene_name = query_gene;
    
        var close_button = "<button class=\"close\" title=\"Close\"><span class=\"close\" onclick=\"$('#jbrowse_frame, #gff_html_card').hide();\">&times;</span></button><br>";
        $("#jbrowse_frame").html(close_button+"<a class=\"float-left jbrowse_link\" href=\"/jbrowse/?data=data%2F"+jb_dataset+"&loc="+jb_gene_name+"&tracks=DNA%2Ctranscripts&highlight=\">Full screen</a><iframe class=\"jb_iframe\" src=\"/jbrowse/?data=data%2F"+jb_dataset+"&loc="+jb_gene_name+"&tracks=DNA%2Ctranscripts&highlight=\" name=\"jbrowse_iframe\"><p>Your browser does not support iframes.</p> </iframe>");

        $('#jbrowse_frame').show();
        $('#gff_html_card').show();
        
      }
    });
    
  }; // end ajax_call


    $('#get_gene_coords').click(function() {
    
    var query_gene = $('#autocomplete_gene').val();
    // var gff_file = "<?php //echo "$gff_file"; ?>";
    // var jb_dataset = "<?php //echo "$jb_dataset"; ?>";

    if (query_gene === "") {
      $("#search_input_modal").html( "No input provided in the gene coordinates" );
      $('#no_gene_modal').modal();
      return false;
    }
    get_gff_ajax_call(query_gene,gff_file,jb_dataset);
    
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