<?php include_once realpath("../header.php");?>

<div class="width900">
	<br>
	<br>
			<a href="/easy_gdb/help/00_help.php"><i class='fas fa-reply' style='color:#229dff'></i> Back to help</a>
			<br>
			<br>
			<h1 style="font-size:26px">Expression Comparator</h4>
			<br>
			<p style="text-align:justify">
				The expression comparator tool (<a href="#input_fig1">Figure 1</a>) allows the user to provide a list of genes to compare their expression values between multiple samples along all available datasets. The datasets information can be consulted at the <kbd>Datasets</kbd> tab. The input form assists users to autocomplete gene names and add them either to the query gene or to the fold change calculator list. Additionally, multiple gene names can be pasted into the boxes by the user. Clicking on the <kbd>Compare</kbd> button will return the output for those genes.
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/input_comparator.png";?>' width="100%"></a>
				<br>
				Figure 1. Expression comparator main page.
				<br>
				<br>
				<br>
			</center>
			<p>
				There are two ways to look for gene comparison:
				<ul>
					<li> 2.1 <a href="#foldchange">With fold change calculation. </a>
					<li> 2.2 <a href="#notfoldchange">Without fold change calculation. </a>
				</ul>
			</p>
			<h4 id="foldchange" class="p_font18">
				<b>2.1 With fold change calculation.</b>
			</h4>
			<p style="text-align:justify">
				The user can specify a gene ID or a list of gene IDs - housekeeping genes, for example - to be used for normalization by fold change calculation. In this case, the expression results will be relative to the gene(s) used as reference(s). It can also be chosen to apply log2-transformation to the data (<a href="#lines_fig2">Figure 2</a>). In this case, genes with an expression value that tends to infinity will be represented by 999.99.
      </p>
			<center>
				<img id="lines_fig2" src='<?php echo "/easy_gdb/help/help_images/lines_comparator.png";?>' width="100%"></a>
				<br>
				Figure 2. Expression comparator lines plot not applying log2 (left) and applying log2 (right).
				<br>
				<br>
				<br>
				</center>
				<p style="text-align:justify">
				In cases where the expression values and lines of some genes hide the information of others, it is possible to place the cursor over the gene names of the legend on top of the graph to highlight the selected gene. It is also possible to click on these gene names to show and hide the data in the graph (<a href="#lines_fig3">Figure 3</a>). On the other hand, moving the cursor over the data points will show the values of all genes in the experimental sample selected. Additionally, the graph can be downloaded in different formats (SVG, CSV or PNG) to save an image of the plot or its data.
				</p>
				<center>
				<img id="lines_fig3" src='<?php echo "/easy_gdb/help/help_images/lines_comparator_highlight.png";?>' width="100%"></a>
				<br>
				Figure 3. Expression comparator lines plot highlighting and hiding gene5.
				<br>
				<br>
				<br>
			</center>
			<p style="text-align:justify">
				Other visualizations tools include <a href="/easy_gdb/help/01_gene_expression.php#heatmap">Heatmap</a>, <a href="/easy_gdb/help/01_gene_expression.php#replicates">Replicates</a> and <a href="/easy_gdb/help/01_gene_expression.php#average">Average values</a>.
			</p>
			<br>
			<h4 id="notfoldchange" class="p_font18">
				<b>2.2 Without fold change calculation.</b>
			</h4>
			<p style="text-align:justify">
				The user can choose not to specify a gene ID to be used for normalization. In this case, the visualization methods are the same but the expression values won't be referred to a gene.
			</p>
			<br>
			<br>
			<br>
      <!-- <h6 style="text-align:right">
        <a href="/easy_gdb/help/03_gene_lookup.php">Continue to gene version lookup <i class='fas fa-share' style='color:#229dff'></i></a>
      </h6> -->
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
