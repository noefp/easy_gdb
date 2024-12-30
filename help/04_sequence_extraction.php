<?php include_once realpath("header_help.php");
// include_once realpath("../header.php")?>

<div class="width900">
	<br>
	<br>
			<a class="pointer_cursor" href="/easy_gdb/help/00_help.php" style='color:#229dff'><i class='fas fa-reply' style='color:#229dff'></i> Go to help</a>
			<br>
			<br>
			<h1 style="font-size:26px">Sequence Extraction </h4>
			<br>
			<p style="text-align:justify">
				This tool enables the user to download a list of gene identifiers (IDs).
				It is works pasting the gene ID and picking the appropriate Dataset (<a href="#input_fig1">Figure 1</a>). Multiple gene ID can be pasted into the box.
				Once done, clicking on <kbd>Download</kbd> button, the file will download in FASTA format.
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/sequence_extraction.png";?>' width="100%"></a>
				<br>
				Figure 1. Sequence_extraction main page.
				<br>
				<br>
				<br>
			</center>
      <!-- <h6 style="text-align:right">
        <a href="/easy_gdb/help/05_annotation_extraction.php">Continue to annotation extraction <i class='fas fa-share' style='color:#229dff'></i></a>
      </h6> -->
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
