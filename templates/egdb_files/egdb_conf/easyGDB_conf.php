<?php
// File paths
$root_path = "/var/www/html"; //use absolute path
$egdb_files_folder = "egdb_files";

$easy_gdb_path = "$root_path/easy_gdb";
$images_path = "/$egdb_files_folder/egdb_images";
$custom_text_path = "$root_path/$egdb_files_folder/egdb_custom_text";
$downloads_path = "downloads";
$annotations_path = "$root_path/annotations";
$blast_dbs_path = "$root_path/blast_dbs";
$lookup_path = "$root_path/lookup"; //from root 
$species_path = "$root_path/$egdb_files_folder/egdb_species";
$lab_path = "$root_path/$egdb_files_folder/egdb_labs";
$json_files_path = "$root_path/$egdb_files_folder/json_files";

  //Expression
$expression_path = "$root_path/expression_data";
$private_expression_path = "$root_path/private_expression_data";
$comparator_lookup = 0;


  //Passport
$passport_path = "$root_path/passport";
$phenotype_imgs_path = "$images_path/descriptors_imgs";

// header: site title, header image and logo
$dbTitle = "Easy GDB";
$header_img = "header_img.png";
$db_logo = "egdb_logo.png";

//SWITCHES

// Select 1 to store annotations in files or 0 to store annotations in a relational database.
$file_database = 1;

// Toolbar
$tb_custom = 0;
$tb_rm_home = 0;

$tb_about = 1;
$tb_downloads = 1;
$tb_species = 1;
$tb_search_box = 0;

$tb_tools = 1;
$tb_search = 1;
$tb_blast = 1;
$tb_jbrowse = 1;
$tb_seq_ext = 1;
$tb_annot_ext = 1;
$tb_lookup = 1;
$tb_enrichment = 0;

//Gene expression atlas
$tb_gene_expr = 1;

// About
$ab_citation = 1;
$ab_labs = 1;

// Expression Atlas
$expr_menu = 1;
$comparator_lookup = 0;

// Expression tools order: 0 for not shown, >=1 to setup the order
$positions=[
  'description' => 1,
  'cartoons' => 2,
  'lines' => 3,
  'cards' => 4,
  'heatmap' => 5,
  'replicartes' => 6,
  'table' => 7
];


//Gene examples
$gene_sample = "";
$input_gene_list="gene1.1
gene2.1
gene3.1";

// Tools
$max_lookup_input = 10000;
$max_extract_seq_input = 10000;
$max_blast_input = 20;
$max_expression_input = 15;
$max_annotation_input = 5000;

// BLAST
$blast_example=">protein_or_DNA
ATGAGTTGTGGGGAGGGATTTATGTCACCACAAATAGAGACTAAAGGAAGTGTTGGATTC
AAAGCGGGTGTTAAAGAGTACAAATTGATTTATTATACTCCTGAATACGAAACCAAAGAT
ACCGATATCTTGGTAACATTTCGAGTAACTCCTCAACCTGGAGTTTCGCCTGTAGAAGCA
GGCTTGAGCGGGCATATCGATACTGACTGATCGATCGATCGTAGCTAGCTAGCTGATCGT
CGTAGCTAGTCGATCGTA";

?>
