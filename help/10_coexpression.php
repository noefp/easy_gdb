<?php include_once realpath("../header.php") ?>

<!-- RETURN -->
<div class="margin-20"></div>

<a class="float-left pointer_cursor" style="text-decoration: underline;" href="/easy_gdb/help/00_help.php">
  <i class="fas fa-reply" style="color:#229dff"></i> Go to help
</a>
<br>

<div class="width900">
  <br><br>
  <h1 style="font-size:26px">Coexpression Search</h1>
  <p style="text-align:justify">
    The coexpression search tool allows users to explore genes with similar expression profiles across selected datasets (<a href="#coex_fig1">Figure 1</a>). By simply entering a gene ID, users can retrieve a table of correlated genes based on predefined thresholds (e.g. Pearson correlation > 0.8).
    The tool is particularly useful to investigate potential functional relationships or regulatory modules within a species.
  </p>

  <center>
    <img id="coex_fig1" style="border:1px solid #555" src='<?php echo "/easy_gdb/help/help_images/coex_input.png";?>' width="100%">
    <br>
    <b>Figure 1.</b> Coexpression search main page.
    <br><br><br>
  </center>

  <p style="text-align:justify">
    Results are displayed in an interactive table (<a href="#coex_fig2">Figure 2</a>) that supports sorting, filtering, and exporting. Users can download the table in multiple formats (CSV, Excel, PDF), or copy/print the results directly.
    Clicking on a gene ID in the results will redirect to its detailed gene page.
  </p>

  <center>
    <img id="coex_fig2" style="border:1px solid #555" src='<?php echo "/easy_gdb/help/help_images/coex_output.png";?>' width="100%">
    <br>
    <b>Figure 2.</b> Example of coexpression results table with interactive features.
    <br><br><br>
  </center>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
