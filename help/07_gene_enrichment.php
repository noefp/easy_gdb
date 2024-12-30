<?php include_once realpath("header_help.php");
// include_once realpath("../header.php")?>

<div class="width900">
	<br>
	<br>
			<a class="pointer_cursor" href="/easy_gdb/help/00_help.php" style='color:#229dff'><i class='fas fa-reply' style='color:#229dff'></i> Go to help</a>
			<br>
			<br>
      <h1 style="font-size:26px">Gene Set Enrichment</h4>
			<br>
			<p style="text-align:justify">
				The gene set enrichment is a tool similar to gene lookup, it is possible to turn some genes to others.	
				The main page of the gene set enrichment (<a href="#input_fig1">Figure 1</a>) allows the user to provide a list of genes to perform statistical enrichment analysis in <a href="https://biit.cs.ut.ee/gprofiler/page/docs">g:Profiler</a>. 
				Multiple gene names can be pasted into the box. Then, the user can choose one of the species available for Gene Ontology enrichment and convert their gene IDs in case they are not of any of the species supplied. 
				Clicking on the <kbd>Submit</kbd> button will redirect the user to the <a href="https://biit.cs.ut.ee/gprofiler/gost">g:Profiler</a> main page, in a new tab, where the analysis will be made (<a href="#output_fig2">Figure 2</a>).
				For more info, consult: <a href="https://biit.cs.ut.ee/gprofiler/page/docs" target="_blank">Welcome to g:Profiler</a>
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/input_enrichment.png";?>' width="100%"></a>
				<br>
				Figure 1. Gene set enrichment main page.
				<br>
				<br>
				<br>
				<img id="output_fig2" src='<?php echo "/easy_gdb/help/help_images/output_enrichment.png";?>' width="100%"></a>
				<br>
				Figure 2. Gene set enrichment output example.
				<br>
				<br>
				<br>
			</center>
      <br>
      <!-- <h6 style="text-align:right">
        <a href="/easy_gdb/help/08_gene_expression.php">Continue to expression viewer <i class='fas fa-share' style='color:#229dff'></i></a>
      </h6> -->
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
