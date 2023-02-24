<?php include_once realpath("../header.php");?>

<div class="width900">
  <br>
  <br>
			<a href="/easy_gdb/help/00_help.php"><i class='fas fa-reply' style='color:#229dff'></i> Back to help</a>
			<br>
			<br>
      <h1 style="font-size:26px">Explore gene expression</h4>
			<br>
			<p>
				The main page of the expression viewer tool (<a href="#input_fig1">Figure 1</a>) allows the user to choose between all available datasets and provide a list of genes to compare their expression values in multiple visualization tools. 
        The datasets information can be consulted in the <kbd>Datasets</kbd> link in the menu toolbar or in the Dataset information link in the Gene Expression Atlas input page.
        The input form assists users to autocomplete gene names and add them to the input gene list. 
        Additionally, multiple gene names can be pasted into the box by the user. 
        Clicking on the <kbd>Get Expression</kbd> button will return the expression output for those genes.
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "help_images/input.png";?>' width="100%"></a>
				<br>
				Figure 1. Expression viewer main page.
				<br>
				<br>
				<br>
			</center>
			<p>
				The expression viewer results provide a description of the experimental conditions of the selected dataset and five visualization methods:
				<ul>
					<li> 1.1 <a href="#lines">Lines. </a>
					<li> 1.2 <a href="#cards">Expression cards. </a>
					<li> 1.3 <a href="#heatmap">Heatmap. </a>
					<li> 1.4 <a href="#replicates">Replicates. </a>
					<li> 1.5 <a href="#average">Average values. </a>
				</ul>
			</p>
			<br>
			<h4 id="lines" class="p_font18">
				<b>1.1 Lines visualization</b>
			</h4>
			<p>
				This output allows to compare the expression of all genes (<a href="#lines_fig1">Figure 2</a>). In cases where the expression values and lines of some genes hide the information of others, it is possible to place the cursor over the gene names of the legend on top of the graph to highlight the selected gene. It is also possible to click on these gene names to show and hide the data in the graph (<a href="#lines_fig2">Figure 3</a>). On the other hand, moving the cursor over the data points will show the values of all genes in the experimental sample selected. Additionally, the graph can be downloaded in different formats (SVG, CSV or PNG) to save an image of the plot or its data.
      </p>
			<center>
				<img id="lines_fig1" src='<?php echo "$images_path/help/lines.png";?>' width="80%"></a>
				<br>
				Figure 2. Expression viewer lines plot.
				<br>
				<br>
				<br>
				<img id="lines_fig2" src='<?php echo "$images_path/help/lines_example.png";?>' width="100%"></a>
				<br>
				Figure 3. Expression viewer lines plot highlighting and hiding gene1.
				<br>
				<br>
				<br>
			</center>
			<h4 id="cards" class="p_font18">
				<b>1.2 Expression cards</b>
			</h4>
			<p>
				In this output, the user can select each one of the genes to see their expression values together with pictures showing their phenotype in the experimental conditions or drawings representing the tissue (<a href="#cards_fig3">Figure 4</a>). The highest value is highlighted in a golden card and the lowest ones in black cards.
			</p>
			<center>
				<img id="cards_fig3" src='<?php echo "$images_path/help/cards.png";?>' width="80%"></a>
				<br>
				Figure 4. Expression cards in the expression viewer.
				<br>
				<br>
				<br>
			</center>
			<h4 id="heatmap" class="p_font18">
				<b>1.3 Heatmap</b>
			</h4>
			<p>
				This output allows a simultaneous comparison of all genes and experimental conditions using a color scale that separates expression values in different ranges (<a href="#heatmap_fig4">Figure 5</a>). Moving the cursor over the different color ranges in the legend will highlight the expression values within that range (<a href="#heatmap_fig5">Figure 6</a>). Three different color palettes are available. Additionally, the graph can be downloaded in different formats (SVG, CSV or PNG) to save an image of the plot or its data.
			</p>
			<center>
				<img id="heatmap_fig4" src='<?php echo "$images_path/help/heatmap.png";?>' width="80%"></a>
				<br>
				Figure 5. Heatmap visualization of the expression viewer.
				<br>
				<br>
				<br>
				<img id="heatmap_fig5" src='<?php echo "$images_path/help/heatmap_example.png";?>' width="100%"></a>
				<br>
				Figure 6. Heatmap visualization of the expression viewer highlighting two ranges.
				<br>
				<br>
				<br>
			</center>
			<h4 id="replicates" class="p_font18">
				<b>1.4 Replicates</b>
			</h4>
			<p>
				This output allows the inspection of the replicates of the selected gene (<a href="#replicates_fig6">Figure 7</a>). Moving the cursor over the data points will show the value of that replicate in the tissue selected. The graph can be downloaded in different formats (SVG or PNG) to save an image of the plot.
			</p>
			<center>
				<img id="replicates_fig6" src='<?php echo "$images_path/help/replicates.png";?>' width="80%"></a>
				<br>
				Figure 7. Expression values of replicates in the expression viewer tool.
				<br>
				<br>
				<br>
			</center>
			<h4 id="average" class="p_font18">
				<b>1.5 Average values</b>
			</h4>
			<p>
				This last output provides a table with the values of each gene in each experiment (<a href="#average_fig7">Figure 8</a>). This table can be downloaded in several formats (CSV, Excel, PDF), can be copied to the clipboard, filtering out unwanted columns and searching within the information in the table.
			</p>
			<center>
				<img id="average_fig7" src='<?php echo "$images_path/help/average.png";?>' width="80%"></a>
				<br>
				Figure 8. Average values table in the expression viewer tool.
				<br>
				<br>
				<br>
			</center>
			<br>
			<h6 style="text-align:right">
				<a href="/easy_gdb/help/02_expression_comparator.php">Continue to expression comparator <i class='fas fa-share' style='color:#229dff'></i></a>
			</h4>
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
