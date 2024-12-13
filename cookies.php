<?php include_once realpath("header.php");?>

<br>

<div class="page_container">
<h3>Cookies</h3>
<br>

<?php
  if ( file_exists("$custom_text_path/custom_cookies.php") ) {
    include_once realpath("$custom_text_path/custom_cookies.php");
  }
  else {
    echo "<p style=\"font-size:20px\">";
    echo "This site only uses functional cookies required for login control and needed cookies from third party tools such as JBrowse.
      JBrowse uses cookies to remember your configuration in the genome browser, such as the tracks loaded and the position in the genome.
      When using this site you accept the use of the cookies.";
    echo "</p>";
  }
?>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
