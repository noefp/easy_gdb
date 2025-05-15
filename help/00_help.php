<?php include_once realpath("../header.php");?>

<div class="width900">
  <br>
  <br>
  <h1 style="font-size:26px;text-align:center">Help</h4>
	<br>
  <p style="text-align:justify">
    These help page explain the way to query the different tools of gene and expression data and how to visualize the results.
  </p>
	<ul>
	<?php 
	if (file_exists($json_files_path."/customization/custom_help.json"))
	{
		$json = file_get_contents("$json_files_path/customization/custom_help.json");
		$json = json_decode($json, true);
		asort($json);
		foreach($json as $key => $value) {
			if($value!=0)
			{
				switch($key){
					case 'search':
						echo "<li><a href=\"/easy_gdb/help/01_search.php\"> Search</a>";
						break;

					case 'blast':
						echo "<li><a href=\"/easy_gdb/help/02_blast.php\"> Blast</a>";
						break;
					case 'jbrowse':
						echo "<li><a href=\"/easy_gdb/help/03_genome_browser.php\"> Genome Browser</a>";
						break;
					case 'seq_ext':
						echo "<li><a href=\"/easy_gdb/help/04_sequence_extraction.php\"> Sequence Extraction</a>";
						break;
					case 'annot_ext':
						echo "<li><a href=\"/easy_gdb/help/05_annotation_extraction.php\"> Annotation Extraction</a>";
						break;
					case 'lookup':
						echo "<li><a href=\"/easy_gdb/help/06_gene_lookup.php\"> Gene version lookup</a>";
						break;
					case 'enrichment':
						echo "<li><a href=\"/easy_gdb/help/07_gene_enrichment.php\"> Gene set enrichment</a>";
						break;
					case 'gene_expr':
						echo "<li><a href=\"/easy_gdb/help/08_gene_expression.php\"> Expression viewer</a>";
						break;
					case 'comparator_lookup':
						echo "<li><a href=\"/easy_gdb/help/09_expression_comparator.php\"> Expression comparator</a>";
						break;
          case 'coexpression':
            echo "<li><a href=\"/easy_gdb/help/10_coexpression.php\"> Coexpression Search</a>";
            break;
				}
			}
		}
		
	} else
	{
		echo "<li><a href=\"/easy_gdb/help/01_search.php\"> Search</a>";
		echo "<li><a href=\"/easy_gdb/help/02_blast.php\"> Blast</a>";
		echo "<li><a href=\"/easy_gdb/help/03_genome_browser.php\"> Genome Browser</a>";
		echo "<li><a href=\"/easy_gdb/help/04_sequence_extraction.php\"> Sequence Extraction</a>";
		echo "<li><a href=\"/easy_gdb/help/05_annotation_extraction.php\"> Annotation Extraction</a>";
		echo "<li><a href=\"/easy_gdb/help/06_gene_lookup.php\"> Gene version lookup</a>";
		echo "<li><a href=\"/easy_gdb/help/07_gene_enrichment.php\"> Gene set enrichment</a>";
		echo "<li><a href=\"/easy_gdb/help/08_gene_expression.php\"> Expression viewer</a>";
		echo "<li><a href=\"/easy_gdb/help/09_expression_comparator.php\"> Expression comparator</a>";
    echo "<li><a href=\"/easy_gdb/help/10_coexpression.php\"> Coexpression Search</a>";
	}
?>
	</ul>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
