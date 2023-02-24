<?php include_once realpath("../header.php");?>

<div class="width900">
	<br>
	<br>
			<a href="/easy_gdb/help/00_help.php"><i class='fas fa-reply' style='color:#229dff'></i> Back to help</a>
			<br>
			<br>
      <h1 style="font-size:26px">Gene Set Enrichment</h4>
			<br>
			<p style="text-align:justify">
				The main page of the gene set enrichment (<a href="#input_fig1">Figure 1</a>) allows the user to provide a list of genes to perform statistical enrichment analysis in <a href="https://biit.cs.ut.ee/gprofiler/page/docs">g:Profiler</a>. Multiple gene names can be pasted into the box. Then, the user can choose one of the species available for Gene Ontology enrichment and convert their gene IDs in case they are not of any of the species supplied. Clicking on the <kbd>Submit</kbd> button will redirect the user to the <a href="https://biit.cs.ut.ee/gprofiler/gost">g:Profiler</a> main page where the analysis will be made (<a href="#output_fig2">Figure 2</a>).
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
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
