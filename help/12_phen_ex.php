<?php include_once realpath("header_help.php");
// include_once realpath("../header.php")?>


<div class="width900">
    <br>
    <br>
            <a class="pointer_cursor" href="/easy_gdb/help/00_help.php" style='color:#229dff'><i class='fas fa-reply' style='color:#229dff'></i> Go to help </a>
            <br>
            <br>
            <h1 style="font-size:26px">Phenotype Extraction </h4>
            <br>
            <p style="text-align:justify">
                The main page of the Phenotype Extraction tool (<a href="#input_fig1">Figure 1</a>) allows the user to query available information within the datasets and extract phenotypic data tables in CSV format. These tables can subsequently be used for association analyses such as GWAS. It is very user-friendly; the user simply needs to select the species of interest and the dataset.
            </p>
            <center>
                <img id="input_fig1" src='<?php echo "/easy_gdb/help/help_images/phen_ex_main_page.png";?>' width="100%"></a>
                <br>
                Figure 1. Phenotype Extraction main page.
                <br>
                <br>
                <br>
            </center>
            <p style="text-align:justify">
                Following the initial selection, the user can choose one or multiple phenotype traits (<a href="#input_fig2">Figure 2</a>).
            </p>
            <center>
                <img id="input_fig2" src='<?php echo "/easy_gdb/help/help_images/phen_ex_select_trait.png";?>' width="100%"></a>
                <br>
                Figure 2. Select phenotype traits.
                <br>
                <br>
                <br>
            </center>
            <p style="text-align:justify">
                Finally, the user selects the accessions of interest, either through the "Browse & search" section (<a href="#input_fig3">Figure 3</a>) or the "Paste list" section (<a href="#input_fig4">Figure 4</a>), and clicks the <kbd>Generate phenotype CSV</kbd> button to create the downloadable file.
            </p>
            <center>
                <img id="input_fig3" src='<?php echo "/easy_gdb/help/help_images/phen_ex_select_acc_brw.png";?>' width="100%"></a>
                <br>
                Figure 3. Select accessions in Browse & search section.
                <br>
                <br>
                <br>
            </center>
            <br>
            <center>
                <img id="input_fig4" src='<?php echo "/easy_gdb/help/help_images/phen_ex_select_acc_pl.png";?>' width="100%"></a>
                <br>
                Figure 4. Select accessions in Paste list section.
                <br>
                <br>
                <br>
            </center>
            <p style="text-align:justify">
                On the download page, the user will find information regarding the generated file, as well as warnings indicating any traits that might have been modified during the file generation process. By clicking the <kbd>Download CSV</kbd> button, the user can download the file (<a href="#input_fig5">Figure 5</a>).
            </p>
            <center>
                <img id="input_fig5" src='<?php echo "/easy_gdb/help/help_images/phen_ex_download_section.png";?>' width="100%"></a>
                <br>
                Figure 5. Download section.
                <br>
                <br>
                <br>
            </center>
            <br>
      </div>

<?php include_once realpath("$easy_gdb_path/footer.php");?>