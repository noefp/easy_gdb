<!-- HEADER -->
<?php include_once realpath("../../header.php");?>
<?php include_once realpath("$root_path/easy_gdb/tools/common_functions.php");?>

<!-- RETURN AND HELP -->
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/10_coexpression.php" target="_blank">
    <i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help
  </a>
</div>

<a class="float-left pointer_cursor" style="text-decoration: underline;" onClick="history.back()">
  <i class="fas fa-reply" style="color:#229dff"></i> Back to input
</a>
<br>

<!-- HTML -->
<h1 class="text-center margin-20">Coexpression Search <i class="fas fa-network-wired" style="color:#555;"></i></h1>
<div class="page_container">


<!-- GET INPUT -->
<?php
  $raw_input = trim($_GET["txtGenes"]);
  $quoted_search = 0;
  if ( preg_match('/^".+"$/',$raw_input ) ) {
    $quoted_search = 1;
  }
?>

<?php
  $search_input = test_input2($raw_input);

  if ($search_input) {
    echo '
    <div class="alert alert-primary" role="alert" style="display:block;">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close" title="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h3 style="display:inline">Search input</h3>
      <div class="card-body" style="padding-top:10px; padding-bottom:0;">' . $search_input . '</div>
    </div>';
  }
?>

<?php
  $lookup_file = $_GET["get_dataset"];
  $raw_input = trim($_GET["txtGenes"]);
  $quoted_search = 0;
  if ( preg_match('/^".+"$/',$raw_input ) ) {
    $quoted_search = 1;
  }
  $search_input = test_input($raw_input);

  //INCLUDE COEX_OPERATIONS
  if(strlen($search_input)==0) {
  echo '<div class="alert alert-danger" role="alert" style="text-align:center">
          No genes to search provided
        </div><br>';  }
  else {
    include_once realpath("coex_operations.php");
  }
?>

<br>
<br>
</div>
<!-- END HTML -->


<!-- CSS -->
<style>
   table.dataTable td  {
    max-width: 500px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;
    vertical-align: middle;
    padding-left: 15px !important;
    padding-right: 15px !important;
    word-break: keep-all; 
  }
  
    .td-tooltip {
      cursor: pointer;
    }
</style>


<!-- JS DATATABLE -->
<script src="../../js/datatable.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    datatable("#tblCorrelations", "1");

    setTimeout(function () {
      $('#tblCorrelations').DataTable().order([2, 'desc']).draw();
    }, 100);  // Set to 200 if necessary

    $(".td-tooltip").tooltip();
  });
</script>


<!-- FOOTER -->
<?php include_once realpath("../../footer.php");?>