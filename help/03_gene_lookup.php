<?php include_once realpath("../header.php");?>

<div class="width900">
	<br>
	<br>
			<a href="/easy_gdb/help/00_help.php"><i class='fas fa-reply' style='color:#229dff'></i> Back to help</a>
			<br>
			<br>
      <h1 style="font-size:26px">Gene Version Lookup</h4>
			<br>
			<p style="text-align:justify">
				The gene version lookup tool (<a href="#input_fig1">Figure 1</a>) allows the user to provide a list of genes and select one of the available datasets to get those gene IDs in the version of the required genome. Clicking on the <kbd>search</kbd> button will return the table output for those genes. This table can be downloaded in several formats (CSV, Excel, PDF), can be copied to the clipboard, filtering out unwanted columns and searching within the information in the table.
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/input_lookup.png";?>' width="100%"></a>
				<br>
				Figure 1. Gene version lookup main page.
				<br>
				<br>
				<br>
			</center>
			<h6 style="text-align:right">
				<a href="/easy_gdb/help/04_gene_enrichment.php">Continue to gene set enrichment <i class='fas fa-share' style='color:#229dff'></i></a>
			</h4>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
