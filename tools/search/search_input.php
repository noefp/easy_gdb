<?php include_once realpath("../../header.php");?>
<?php include_once realpath("search_info_modal.php");?>

<br>
<br>
<div style="margin:auto; max-width:900px">

<form id="ppatens_search_form" action="search_output.php" method="get">
  <div class="form-group">
    <label for="search_box" style="font-size:16px">Insert a gene ID or annotation keywords</label> <button type="button" class="info_icon" data-toggle="modal" data-target="#search_help">i</button>
    <input type="search_box" class="form-control" id="search_box" name="search_keywords">

  </div>

  <button type="submit" class="btn btn-info float-right">Search</button>
  <br>
  <br>
  <br>
</form>
</div>

<!--  no input gene modal -->
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

<?php include_once realpath("$easy_gdb_path/footer.php");?>

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
  	line-height:18px;
  	text-align:center;
  /*  padding-bottom:2px;*/
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

<script>

$(document).ready(function () {

  //check input gene before sending form
  $('#ppatens_search_form').submit(function() {
    var gene_id = $('#search_box').val();
    
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
    else {
      return true;
    }
  });

});

</script>
