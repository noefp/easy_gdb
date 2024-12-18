<?php include_once realpath("../header.php");?>

<div class="width900">
	<br>
	<br>
			<a class="pointer_cursor" onclick="window.history.back();" style='color:#229dff'><i class='fas fa-reply' style='color:#229dff'></i> Back</a>
			<br>
			<br>
			<h1 style="font-size:26px">Blast </h4>
			<br>
			<p style="text-align:justify">
				The Blast tool (<a href="#input_fig1">Figure 1</a>) allows the user to paste a sequence a run Blast to search it.
				The user can choose de dataset and de Blast program to run (<a href="#input_fig2">Figure 2</a>). Also it can configurate the Blast options: Max hit number, Max e value and matrix.
				Once setup is done, click on <kbd>Blast</kbd>. 
				If the combination chosen of dataset and the program is incompatible, it will appear an emergent window with de incompatibility.
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/blast.png";?>' width="100%"></a>
				<br>
				Figure 1. Blast main page.
				<br>
				<br>
				<br>
			</center>
			<center>
				<img id="input_fig2" src='<?php echo "/easy_gdb/help/help_images/blast_options.png";?>' width="100%"></a>
				<br>
				Figure 2. Blast options of dataset and program.
				<br>
				<br>
				<br>
			</center>
			<p>
				The blast result appear as a graphic in different colours depending of the quality of the alineation, a table with the diferent genes alineated and its values and the every result in text format (<a href="#output_fig3">Figure 3</a>).
				The results obtained can be download in tabular format (at the top to the right of the page).
			</p>
			<center>
				<img id="output_fig3" src='<?php echo "/easy_gdb/help/help_images/blast_result.png";?>' width="100%"></a>
				<br>
				Figure 3. Blast result example, using Blastn program, nucleotides datasets and default Blast options.
				<br>
				<br>
				<br>
			</center>
      <!-- <h6 style="text-align:right">
        <a href="/easy_gdb/help/03_genome_browser.php">Continue to genome browser <i class='fas fa-share' style='color:#229dff'></i></a>
      </h6> -->
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
