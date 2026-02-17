<nav class="navbar navbar-expand-md bg-dark navbar-dark sticky-top" style="padding-left:10px">
 
<!-- warning debug mode  (if set to 1, save all errors and warnings logs. If set to 0, not save warnings logs but save errors logs)-->
<?php 


  if (!isset($warning_debug) || !$warning_debug) {
   ini_set('error_reporting', E_ALL & ~E_WARNING & ~E_NOTICE); // show all errors except notices (notices are warnings)
}else{
   ini_set('error_reporting', E_ALL); // show all errors
}
?>

<?php 
  if (isset($db_logo) && $db_logo && file_exists(realpath("$root_path/$images_path/$db_logo"))) {      
    echo "<a class=\"navbar-brand\" href=\"/easy_gdb/index.php\" style=\"margin-right:5px\"><img id=\"site_logo\" src=$images_path/$db_logo alt=\"DB_Logo\" style=\"height:25px; vertical-align:text-bottom;\"></a>";
  }
  
?>

<?php 
  if (isset($tb_rm_home) && !$tb_rm_home && isset($dbTitle)) {
    echo "<a class=\"navbar-brand\" href=\"/easy_gdb/index.php\"> $dbTitle</a>";
  }

?>

  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <?php 
        if (isset($tb_species) && $tb_species) {
          echo '<li class="nav-item"><a class="nav-link" href="/easy_gdb/species.php">Species</a></li>';
        }
        
        if (isset($tb_tools) && $tb_tools) {
          echo '<li class="nav-item dropdown">';
          echo '<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Tools</a>';
          echo '<div class="dropdown-menu">';
          
          if (isset($tb_search) && $tb_search) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/search/search_input.php">Search</a>';
          }
          if (isset($tb_blast) && $tb_blast) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/blast/blast_input.php">BLAST</a>';
          }
          if (isset($tb_jbrowse) && $tb_jbrowse) {
            echo '<a class="dropdown-item jbrowse_link" href="/jbrowse/" target="_blank">Genome Browser</a>';
          }
          if (isset($tb_jbrowse2) && $tb_jbrowse2) {
            echo '<a class="dropdown-item jbrowse_link" href="/jbrowse2/" target="_blank">Synteny Viewer</a>';
          }
          if (isset($tb_seq_ext) && $tb_seq_ext) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/fasta_download.php">Sequence Extraction</a>';
          }
          if (isset($tb_annot_ext) && $tb_annot_ext) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/annot_input_list.php">Annotation Extraction</a>';
          }
          if (isset($tb_lookup) && $tb_lookup) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/gene_lookup.php">Gene Version Lookup</a>';
          }
          if (isset($tb_enrichment) && $tb_enrichment) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/gene_enrichment.php">Gene Set Enrichment</a>';
          }
          
          echo '</div>';
          echo '</li>';
        }
        
        if (isset($tb_gene_expr) && $tb_gene_expr) {
          echo '<li class="nav-item dropdown">';
            echo '<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Expression Atlas</a>';
            echo '<div class="dropdown-menu">';
            if(!isset($tb_expr_viewer) || $tb_expr_viewer)
              {echo '<a class="dropdown-item" href="/easy_gdb/tools/expression/expression_input.php">Expression viewer</a>';}
            if(!isset($tb_expr_comparator) || $tb_expr_comparator)
              {echo '<a class="dropdown-item" href="/easy_gdb/tools/expression/comparator_input.php">Expression comparator</a>';}
            if(!isset($tb_cv_calculator) || $tb_cv_calculator)
              {echo '<a class="dropdown-item" href="/easy_gdb/tools/expression/cv_calculator_input.php">CV calculator</a>';}
            if(!isset($tb_expr_datasets) || $tb_expr_datasets)
              {echo '<a class="dropdown-item" href="/easy_gdb/tools/expression/expression_menu.php">Datasets</a>';}
            echo '</div>';
          echo '</li>';
        }
        
        if (isset($tb_passport) && $tb_passport) {
          echo '<li class="nav-item dropdown">';
            echo '<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Passport and Phenotype</a>';
            echo '<div class="dropdown-menu">';
            if(!isset($tb_navigation) || $tb_navigation)
              {if(file_exists("$passport_path/germplasm_list.json")) // check if passport germplasm_list file exist 
                      {echo '<a class="dropdown-item" href="/easy_gdb/tools/passport/view_subdirectories.php">Navigation</a>';} // multiple subdirectories (species)
                else  {echo '<a class="dropdown-item" href="/easy_gdb/tools/passport/02_pass_file_to_datatable.php">Navigation</a>';}} // single directory only one species
            if(!isset($tb_search_passport) || $tb_search_passport)
              {echo '<a class="dropdown-item" href="/easy_gdb/tools/passport/passport_search_input.php">Search</a>';}
            echo '</div>';
          echo '</li>';
        }
        
        if (isset($tb_downloads) && $tb_downloads) {
          echo '<li class="nav-item"><a class="nav-link" href="/easy_gdb/downloads.php">Downloads</a></li>';
        }
        
        if (isset($tb_about) && $tb_about) {
          echo '<li class="nav-item"><a class="nav-link" href="/easy_gdb/about.php">About</a></li>';
        }

        if (isset($tb_more) && $tb_more) {
          include_once realpath("$easy_gdb_path/more.php");
        }

        if (isset($tb_help) && $tb_help) {
          echo '<li class="nav-item"><a class="nav-link" href="/easy_gdb/help/00_help.php">Help</a></li>';
        }

        if (isset($tb_custom) && $tb_custom) {
          include_once realpath("$custom_text_path/custom_toolbar.php");
        }

        if (isset($tb_private) && $tb_private) {
          echo '<li class="nav-item"><a id="tbp_link" class="nav-link" href="#"><b>Private links</b></a></li>';
        }
      ?>
      
    </ul>
  
  <?php
  if (isset($tb_search_box) && $tb_search_box) {
    if (isset($file_database) && $file_database) {
      echo '<div class="ml-auto">';
      // echo '<div class="d-flex mt-2 mt-md-0 justify-content-start justify-content-md-end w-100">';
      echo '<form class="input-group" id="egdb_search_file_form" action="/easy_gdb/tools/search/search_output_file.php" method="get" style="width: auto;">';
      echo '<input type="search" class="form-control" id="search_file_box" name="search_keywords" placeholder="Search">';
      echo '<input type="hidden" name="search_all" value="1">';
      echo '<div class="input-group-append">';
      echo '<button type="submit" class="btn btn-info">';
      echo '<i class="fa fa-search" style="font-size:16px;color:white"></i>';
      echo '</button>';
      echo '</div>';
      echo '</form>';
      echo '</div>';
    }
    else {
        echo '<form class="ml-auto form-inline" id="egdb_search_form" action="/easy_gdb/tools/search/search_output.php" method="get">';
        echo '<input type="search_box" class="form-control mr-sm-2" id="search_box" name="search_keywords" placeholder="Search">';
        echo '<button type="submit" class="btn btn-info"><i class="fa fa-search" style="font-size:16px;color:white"></i></button>';
        echo '</form>';
      }
    }

    // if ($tb_login) {
    //     echo'<a id="login_link" class="ml-auto" style="color:white; cursor:pointer" data-toggle="modal" data-target="#loginModal">Log In <i class="fa fa-sign-in-alt" style="font-size:16px;color:white"></i></a>';
    //     echo'<a id="logout_link" class="ml-auto" style="color:white; cursor:pointer; display:none">Log Out <i class="fa fa-sign-out-alt" style="font-size:16px;color:white"></i></a>';
    // }
  ?>
  
  </div>

</nav>

<style>
   @media (max-width: 575px) {
     #search_box {
       width: 193px;
       margin-right: .5rem!important;
     }
   }
   
   #tbp_link {
     display:none;
     color:#d44;
   }
  
   #tbp_link:hover {
     color:#f44;
   }
  
</style>