<!doctype html>
<html lang="en">
<?php 
if (file_exists($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php") ) {
  include_once($_SERVER['DOCUMENT_ROOT']."/easy_gdb/configuration_path.php");
} elseif (file_exists($_SERVER['DOCUMENT_ROOT']."/configuration_path.php") ) {
  include_once($_SERVER['DOCUMENT_ROOT']."/configuration_path.php");
 }
include_once "$conf_path/easyGDB_conf.php";
?>

  <head>
    <title><?php echo "$dbTitle";?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href=<?php echo "$images_path/favicon.ico";?>>

    <!-- compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/easy_gdb/css/easy_gdb.css">
    <link rel="stylesheet" href="/easy_gdb/css/loading_datatable.css">

    <?php 
      if ($custom_css_path && file_exists("$root_path/$custom_css_path")) {
        echo "<link rel=\"stylesheet\" href=\"/$custom_css_path\">";
      }
    ?>




    <!-- <link rel="stylesheet" type="text/css" href="/easy_gdb/css/datatables.css"> -->
    <!-- <link rel="stylesheet" href="/easy_gdb/js/DataTables/Select-1.2.6/css/select.dataTables.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.5/css/colReorder.dataTables.min.css">


    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="/easy_gdb/js/download2.js"></script>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/colreorder/1.5.5/js/dataTables.colReorder.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap.min.js"></script> -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.colVis.min.js"></script>
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.dataTables.min.js"></script> -->
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script> -->
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.foundation.min.js"></script> -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.jqueryui.min.js"></script> -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.semanticui.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  </head>

  <body>

    <div class="container-fluid easygdb-top">
      <!-- <p class="pull-left cover-title">
        <?php //echo "$dbTitle";?>
      </p> -->

      <div style="background: url(<?php echo $images_path."/".$header_img;?>) left bottom; background-size:cover;">
       <img class="cover-img" src=<?php echo "$images_path/$header_img";?> style="visibility: hidden;"/>
       <!-- <a href=<?php //echo "$logo1_link";?> target="_blank"><img class="float-left img-rounded-5" width=<?php //echo "$logo1_width";?> src=<?php //echo "$images_path/$logo1";?> ></a> -->
       <!-- <a href=<?php //echo "$logo2_link";?> target="_blank"><img class="float-right img-rounded-5" width=<?php //echo "$logo2_width";?> src=<?php //echo "$images_path/$logo2";?> ></a> -->
       <!-- <a href=<?php //echo "$logo3_link";?> target="_blank"><img class="float-right institution_logo3 img-rounded-5" width=<?php //echo "$logo3_width";?> src=<?php //echo "$images_path/$logo3";?> ></a> -->
      </div>
    </div>

    <!-- <div id="wrapper"> -->


<?php include_once 'toolbar.php';?>


<div class="page_container">

<div id="jb_cookies_Modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cookies policy</h4>
      </div>
      <div class="modal-body">
        <p>
          Jbrowse uses cookies to remember your configuration in the genome browser, such as track load and position.
          When using Jbrowse in this site you accept the use of these cookies.
        </p>
      </div>
      <div class="modal-footer">
        <a id="jb_ok_cookies" href="/jbrowse/" target="_blank" type="button" class="btn btn-default">OK</a>
      </div>
    </div>

  </div>
</div>

<script>
  jQuery(document).ready(function() {
    
    var jb_link = "/jbrowse/";
    
    $(".jbrowse_link").click(function(event){
      event.preventDefault();
      jb_link = $(this).attr('href');
      $("#jb_ok_cookies").attr('href', jb_link);
      $("#jb_cookies_Modal").modal();
    });
    
    $("#jb_ok_cookies").click(function(event){
      $("#jb_cookies_Modal").modal("hide");
    });

  });
</script>

<style>

  .cover-img {
    height: 277px;
    width:20px;
    overflow: hidden;
  }

  .cover-title {
    position: absolute;
    padding:10px;
    margin-top:200px;
    font-size: 24px;
    color:#fff;
    width: 50%;
    background: black; /* For browsers that do not support gradients */
    background-color: rgba(0, 0, 0, 0.5);
    background: -webkit-linear-gradient(left, rgba(0, 0, 0, 0.8) , rgba(0, 0, 0, 0)); /* For Safari 5.1 to 6.0 */
    background: -o-linear-gradient(right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0)); /* For Opera 11.1 to 12.0 */
    background: -moz-linear-gradient(right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0)); /* For Firefox 3.6 to 15 */
    background: linear-gradient(to right, rgba(0, 0, 0, 0.8) , rgba(0, 0, 0, 0)); /* Standard syntax */
  }

  .easygdb-top {
    background-color: #a7d0e5;
    width: 100%;
    height: 277px;
    padding:0px
  }

  .institution_logo3 {
    right:0px;
    position:absolute;
    top:75px;
  }

  .img-rounded-5 {
    border-radius: 5px;
    margin:10px;
  }

</style>