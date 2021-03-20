    <nav class="navbar navbar-expand-md bg-dark navbar-dark sticky-top" style="padding-left:10px">
      <?php 
        if ($db_logo && file_exists(realpath("$root_path/$images_path/$db_logo"))) {      
          echo "<a class=\"navbar-brand\" href=\"/easy_gdb/index.php\" style=\"margin-right:5px\"><img src=$images_path/$db_logo alt=\"DB_Logo\" style=\"height:25px; vertical-align:text-bottom;\"></a>";
        }
      ?>      

      <a class="navbar-brand" href="/easy_gdb/index.php"><?php echo "$dbTitle";?></a>

      <!-- Toggler/collapsibe Button -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

        <div class="collapse navbar-collapse" id="collapsibleNavbar">
          <ul class="navbar-nav">
            <!-- <li><a href="about.php">About</a></li> -->
            <li class="nav-item"><a class="nav-link" href="/easy_gdb/downloads.php">Downloads</a></li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Tools</a>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="/easy_gdb/tools/search/search_input.php">Search</a>
                <a class="dropdown-item" href="/easy_gdb/tools/blast/blast_input.php">BLAST</a>
                <!-- <a class="dropdown-item" class="jbrowse_link" href="/jbrowse" target="_blank">Genome Browser</a> -->
                <a class="dropdown-item" href="/easy_gdb/tools/fasta_download.php">Sequence extraction</a>
                <a class="dropdown-item" href="/easy_gdb/tools/annot_input_list.php">Annotation extraction</a>
                <a class="dropdown-item" href="/easy_gdb/tools/gene_lookup.php">Gene version lookup</a>
              </div>
            </li>
          </ul>
        
          <form class="form-inline" id="egdb_search_form" action="/easy_gdb/tools/search/search_output.php" method="get">
            <input type="search_box" class="form-control mr-sm-2" id="search_box" name="search_keywords" placeholder="Search">
            <button type="submit" class="btn btn-info"><i class="fa fa-search" style="font-size:16px;color:white"></i></button>
          </form>
        </div>

    </nav>