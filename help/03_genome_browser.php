<?php include_once realpath("../header.php");?>

<div class="width900">
	<br>
	<br>
			<a href="/easy_gdb/help/00_help.php"><i class='fas fa-reply' style='color:#229dff'></i> Back to Help</a>
			<br>
			<br>
			<h1 style="font-size:26px">Genome Browser </h4>
			<br>

			<p style="text-align:justify">
				JBrowse is an external app to IHSM Subtropical DB. Because of that, it appears an emergent window with the advice of cookies policy (<a href="#input_fig1">Figure 1</a>). 
				Clicking in <kbd>OK</kbd> it will redirect to the JBrowse page, it will open in a new tab.
				Once inside, the user can browse in the genoma chosen with the different tracks available, zoom in the genome or search a specific gene (with de dropdown or autocomplete search bar) (<a href="#input_fig2">Figure 2</a>). 
				More information can be consulted in 
      </p>
      <li>
        <a href="https://jbrowse.org/jb2/docs/user_guide/" target="_blank">User guide of JBrowse</a>
      </li>

			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/JBrowse_cookies.png";?>' width="60%"></a>
				<br>
				Figure 1. Cookies policy to accept.
				<br>
				<br>
				<br>
			</center>
			<center>
				<img id="input_fig2" src='<?php echo "/easy_gdb/help/help_images/JBrowse_example.png";?>' width="100%"></a>
				<br>
				Figure 2. JBrowse example.
				<br>
				<br>
				<br>
			</center>
      <!-- <h6 style="text-align:right">
        <a href="/easy_gdb/help/04_sequence_extraction.php">Continue to sequence extraction <i class='fas fa-share' style='color:#229dff'></i></a>
      </h6> -->
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
