<?php include_once realpath("../header.php");?>

<div class="width900">
  	<br>
  	<br>
			<a class="pointer_cursor" onclick="window.history.back();" style='color:#229dff'><i class='fas fa-reply' style='color:#229dff'></i> Back </a>
			<br>
			<br>
      		<h1 style="font-size:26px">Search</h4>
			<br>
			<p style="text-align:justify">
				The main page of the search tool (<a href="#input_fig1">Figure 1</a>) enables the user to look up whatever of all available datasets and it provides a list of genes which contain the word or words used in the quest.
        		It is so easy, the user only have to write on the search bar the word or words which consider necessary and click on <kbd>Search</kbd> button.
				It will appear all data available on IHSM Subtropical DB.
			</p>
			<center>
				<img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/search_mainpage.png";?>' width="100%"></a>
				<br>
				Figure 1. Search main page.
				<br>
				<br>
				<br>
			</center>
			<p style="text-align:justify">
				There are some actions possible in the toolbar, at the top of information table. 
				The user can copy the information or download it in a file (CVS, excel or PDF) or print it. 
				The user can also configurate the way of displaying the information. In (<a href="#input_fig2">Figure 2</a>) 
			</p>
			<center>
				<img id="input_fig2" src='<?php echo "/easy_gdb/help/help_images/search_example.png";?>' width="100%"></a>
				<br>
				Figure 2. Search example.
				<br>
				<br>
				<br>
			</center>
			<p style="text-align:justify">
				If the user click on one of the output data, it will access to all the info about it.(<a href="#input_fig3">Figure 3</a>)
			</p>
			<center>
				<img id="input_fig2" src='<?php echo "/easy_gdb/help/help_images/search_example_output.png";?>' width="100%"></a>
				<br>
				Figure 3. Search example output.
				<br>
				<br>
				<br>
			</center>
			<br>
      <!-- <h6 style="text-align:right">
        <a href="/easy_gdb/help/02_blast.php">Continue to Blast <i class='fas fa-share' style='color:#229dff'></i></a>
      </h6> -->
</div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>
