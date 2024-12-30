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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">


    <?php 
      if ($custom_css_path && file_exists($custom_css_path)) {
        echo "<link rel=\"stylesheet\" href=\"/$custom_css_path\">";
      }
    ?>
</head>