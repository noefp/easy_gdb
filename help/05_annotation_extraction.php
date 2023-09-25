<?php include_once realpath("../header.php");?>

<div class="width900">
	<br>
	<br>
			<a href="/easy_gdb/help/00_help.php"><i class='fas fa-reply' style='color:#229dff'></i> Back to Help</a>
			<br>
			<br>
      		<h1 style="font-size:26px">Annotation Extraction </h4>
			<br>
			<p style="text-align:justify">
				The main page of the annotation extraction (<a href="#input_fig1">Figure 1</a>) allows the user to search a list of genes identifiers (IDs) and obtain the annotations from different databases (SwissProt, Araport11, TrEMBL and InterPro). 
				Multiple gene ID can be pasted into the box. Clicking on <kbd>Search</kbd> button, it will get the outputs.
				Then, the user can access to an input clicking on the database ID (<a href="#output_fig2">Figure 2</a>), it will redirect to the database page (in a new tab) to get more information. 
				The output results can be copied to the clipboard, downloaded in several formats (CVS, Excel or PDF) and printed. The user can configurate, in the <kbd>Column visibility</kbd> dropdown, which database appears.
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/annotation_extraction.png";?>' width="100%"></a>
				<br>
				Figure 1. Annotation extraction main page.
				<br>
				<br>
				<br>
				<img id="output_fig2" src='<?php echo "/easy_gdb/help/help_images/annotext_example.png";?>' width="100%"></a>
				<br>
				Figure 2. Annotation extraction output example.
				<br>
				<br>
				<br>
			</center>
      <!-- <h6 style="text-align:right">
        <a href="/easy_gdb/help/06_gene_lookup.php">Continue to gene lookup <i class='fas fa-share' style='color:#229dff'></i></a>
      </h6> -->
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
