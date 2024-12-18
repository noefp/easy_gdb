<?php include_once realpath("../header.php");?>

<div class="width900">
  <!-- <br> -->
  <br>
  <h1 style="font-size:26px;text-align:center">Help</h4>
	<br>
  <p style="text-align:justify">
    These help page explain the way to query the different tools of gene and expression data and how to visualize the results. 
  </p>

	<ul>
<?php
  	if($tb_search) echo "<li><a href=\"/easy_gdb/help/01_search.php\"> Search</a>";
  	if($tb_blast) echo "<li><a href=\"/easy_gdb/help/02_blast.php\"> Blast</a>";
  	if($tb_jbrowse) echo "<li><a href=\"/easy_gdb/help/03_genome_browser.php\"> Genome Browser</a>";
  	if($tb_seq_ext) echo "<li><a href=\"/easy_gdb/help/04_sequence_extraction.php\"> Sequence Extraction</a>";
  	if($tb_annot_ext) echo "<li><a href=\"/easy_gdb/help/05_annotation_extraction.php\"> Annotation Extraction</a>";
  	if($tb_lookup ) echo "<li><a href=\"/easy_gdb/help/06_gene_lookup.php\"> Gene version lookup</a>";
  	if($tb_enrichment) echo "<li><a href=\"/easy_gdb/help/07_gene_enrichment.php\"> Gene set enrichment</a>";
  	if($tb_gene_expr) echo "<li><a href=\"/easy_gdb/help/08_gene_expression.php\"> Explore gene expression</a>";
  	if($tb_gene_expr) echo "<li><a href=\"/easy_gdb/help/09_expression_comparator.php\"> Expression comparator</a>";
?>
	</ul>

</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
