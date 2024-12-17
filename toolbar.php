<?php 
// if ($tb_login) {
//   include_once 'login_modal.php';
// }
?>

<nav class="navbar navbar-expand-md bg-dark navbar-dark sticky-top" style="padding-left:10px">
  
<?php 
  if ($db_logo && file_exists(realpath("$root_path/$images_path/$db_logo"))) {      
    echo "<a class=\"navbar-brand\" href=\"/easy_gdb/index.php\" style=\"margin-right:5px\"><img src=$images_path/$db_logo alt=\"DB_Logo\" style=\"height:25px; vertical-align:text-bottom;\"></a>";
  }
?>

<?php 
  if (!$tb_rm_home) {
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
        if ($tb_species) {
          echo '<li class="nav-item"><a class="nav-link" href="/easy_gdb/species.php">Species</a></li>';
        }
        
        if ($tb_tools) {
          echo '<li class="nav-item dropdown">';
          echo '<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Tools</a>';
          echo '<div class="dropdown-menu">';
          
          if ($tb_search) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/search/search_input.php">Search</a>';
          }
          if ($tb_blast) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/blast/blast_input.php">BLAST</a>';
          }
          if ($tb_jbrowse) {
            echo '<a class="dropdown-item jbrowse_link" href="/jbrowse/" target="_blank">Genome Browser</a>';
          }
          if ($tb_seq_ext) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/fasta_download.php">Sequence Extraction</a>';
          }
          if ($tb_annot_ext) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/annot_input_list.php">Annotation Extraction</a>';
          }
          if ($tb_lookup) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/gene_lookup.php">Gene Version Lookup</a>';
          }
          if ($tb_enrichment) {
            echo '<a class="dropdown-item" href="/easy_gdb/tools/gene_enrichment.php">Gene Set Enrichment</a>';
          }
          
          echo '</div>';
          echo '</li>';
        }
        
        if ($tb_gene_expr) {
          echo '<li class="nav-item dropdown">';
            echo '<a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Expression Atlas</a>';
            echo '<div class="dropdown-menu">';
              echo '<a class="dropdown-item" href="/easy_gdb/tools/expression/expression_input.php">Expression viewer</a>';
              echo '<a class="dropdown-item" href="/easy_gdb/tools/expression/comparator_input.php">Expression comparator</a>';
              echo '<a class="dropdown-item" href="/easy_gdb/tools/expression/expression_menu.php">Datasets</a>';
            echo '</div>';
          echo '</li>';
        }
        
        if ($tb_downloads) {
          echo '<li class="nav-item"><a class="nav-link" href="/easy_gdb/downloads.php">Downloads</a></li>';
        }
        
        if ($tb_about) {
          echo '<li class="nav-item"><a class="nav-link" href="/easy_gdb/about.php">About</a></li>';
        }

        if ($tb_more) {
          include_once realpath("$easy_gdb_path/more.php");
        }

        if ($tb_help) {
          include_once realpath("$easy_gdb_path/help/00_help.php");
        }

        if ($tb_custom) {
          include_once realpath("$custom_text_path/custom_toolbar.php");
        }

        if ($tb_private) {
          echo '<li class="nav-item"><a id="tbp_link" class="nav-link" href="#"><b>Private links</b></a></li>';
        }
      ?>
      
    </ul>
  
  <?php
    if ($tb_search_box) {
      echo '<form class="ml-auto form-inline" id="egdb_search_form" action="/easy_gdb/tools/search/search_output.php" method="get">';
        echo '<input type="search_box" class="form-control mr-sm-2" id="search_box" name="search_keywords" placeholder="Search">';
        echo '<button type="submit" class="btn btn-info"><i class="fa fa-search" style="font-size:16px;color:white"></i></button>';
      echo '</form>';
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