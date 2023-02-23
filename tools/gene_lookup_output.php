<?php include realpath('../header.php'); ?>

<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/03_gene_lookup.php"><i class='fa fa-info' style='font-size:20px;color:#229dff'></i> Help</a>
</div>

<a href="/easy_gdb/tools/gene_lookup.php" class="float-left" style="text-decoration: underline;"><i class="fas fa-reply" style="color:#229dff"></i> Back to input</a>
<br>

<h1 class="text-center">Gene Version Lookup</h1>

<div class="page_container" style="margin-top:40px">
  
<div class="data_table_frame">

<?php
$lookup_file = $_POST["lookup_db"];
$input_gene_list = $_POST["txtGenes"];

$gene_hash = [];
$gNamesArr=array_filter(explode("\n",trim($_POST["txtGenes"])),function($gName) {return ! empty($gName);});
// $gNamesArr = explode( "\n",trim($_POST["txtGenes"]) );

if (sizeof($gNamesArr)==0) {
  echo "<h3>The gene list was empty!</h3>";
} 
else {

  // echo "<p>time1: ".time()."</p>";
  $tab_file = file($lookup_file);
  $columns = [];
  $count = 1;
  foreach ($tab_file as $line) {
    $trimmed_line = trim($line);
      $columns = str_getcsv($trimmed_line,"\t");
    
      $count++;
    
      // if ($count < 5) {
      //   echo "<p>line.".$line.".line</p>";
      //   echo "<p>line.".$trimmed_line.".line</p>";
      //
      //   echo "<p>gene1: $columns[0], gene2: $columns[1]</p>";
        $gene_hash[trim($columns[0])] = trim($columns[1]);
        $gene_hash[trim($columns[1])] = trim($columns[0]);
      
      // }
  }
  // echo "<p>time2: ".time()."</p>";

  echo "<table class=\"table\" id=\"tblResults\"><thead><tr><th>input genes</th><th>genes found</th></thead>";

  foreach ($gNamesArr as $input_gene) {
    $converted_gene = $gene_hash[trim($input_gene)];
    
    if ( preg_match("/;/", $converted_gene ) ) {
      
      $multi_genes = explode(";",$converted_gene);
      
      foreach ($multi_genes as $one_gene) {
        echo "<tr style=\"background-color:#FFEFEF\"><td>$input_gene</td><td>".$one_gene."</td></tr>";
      }
    } else {
      echo "<tr><td>$input_gene</td><td>".$converted_gene."</td></tr>";
    }
  }

  echo "</table>";
// echo "<p>time3: ".time()."</p>";

}



?>

</div>
</div>
<br>
<br>
<script type="text/javascript">
  $("#tblResults").dataTable({
  	"dom":'Bfrtip',
    "ordering": false,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print', 'colvis']
  });
  
  $("#tblResults_filter").addClass("float-right");
  $("#tblResults_info").addClass("float-left");
  $("#tblResults_paginate").addClass("float-right");

</script>


<?php include realpath('../footer.php'); ?>
