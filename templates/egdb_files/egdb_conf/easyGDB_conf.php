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

// Custom css file
$custom_css_path = "$egdb_files_folder/css/custom.css";

//Expression
$expression_path = "$root_path/expression_data";
$private_expression_path = "$root_path/private_expression_data";

//Passport
$passport_path = "$root_path/passport/example";
$phenotype_imgs_path = "$images_path/descriptors_imgs";

//Site title, site logo and header image
$dbTitle = "Easy GDB";
$db_logo = "egdb_logo.png";

// Select 0 to disable the header image, or provide an image filename to display it.
$header_img = "header_img.png";

//SWITCHES

// remove citation from welcome page
$rm_citation = 1;

// Select 1 to store annotations in files or 0 to store annotations in a relational database.
$file_database = 1;

// Set to 1 to remove the jbrowse frame from gene.php. Set to 1 if JBrowse was not installed or setup.
$rm_jb_frame = 1;

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

$tb_help = 1;
$tb_more = 0;
$tb_private = 0;

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
  'replicates' => 6,
  'table' => 7
];


//Generic colors and ranges of expression

// This section defines the colors, ranges and range labels for the color palette used generically in the expression viewer and comparator viewer tools.
// The order of the elements is from lowest to highest expression.
// *"colors": is where you place the color code you want to use for each range of expression.
// *"range_text": is the text to be displayed in each of the expression ranges.
// *"ranges": this is an array that contains arrays of 2 spaces in which the lower and upper limits of each range are defined. [low,up].

$colors = ["#eceff1","#b3e5fc","#80cbc4","#ffee58","#ffb74d","#ff8f00","#ff4f00","#cc0000","#D72C79","#801C5A","#6D3917","#443627"];
$range_text =["<1",">=1",">=2",">=5",">=10",">=50",">=100",">=200",">=500",">=1000",">=5000",">=8000"];
$ranges=[[0,0.99],[1,1.99],[2,4.99],[5,9.99],[10,49.99],[50,99.99],[100,199.99],[200,499.99],[500,999.99],[1000,4999.99],[5000,7999.99],[8000,80000]];

// If you want to create a specific color palette for each dataset, you can configure it in the expression_info.json 
// file located in the easy_gdb/json_files/tools folder and there put the variables shown above in the section of each dataset.



// Passport
$tb_passport = 1;
$show_map = 1;
$show_qr = 1;

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

