<?php
// File paths
$root_path = "/var/www/html"; //use absolute path
$easy_gdb_path = "$root_path/easy_gdb";
$blast_dbs_path = "$root_path/blast_dbs";
$downloads_path = "downloads";
$lookup_path = "$root_path/lookup_files"; //from root 
$images_path = "/egdb_files/egdb_img_samples";
$custom_text_path = "$root_path/egdb_files/egdb_custom_text";
$species_path = "$root_path/egdb_files/egdb_species";
$lab_path = "$root_path/egdb_files/egdb_labs";
$annotation_links_path = "$root_path/egdb_files/egdb_template_conf";

// header
$dbTitle = "Easy GDB";
$header_img = "header_img.png";
$db_logo = "egdb_logo.png";

// Toolbar
$tb_about = 1;
$tb_downloads = 1;
$tb_species = 0;
$tb_search_box = 1;
$tb_search = 1;
$tb_blast = 1;
$tb_jbrowse = 1;
$tb_seq_ext = 1;
$tb_annot_ext = 1;
$tb_lookup = 1;
$tb_more = 0;

// About
$ab_citation = 1;
$ab_labs = 1;

//Gene examples
$gene_sample = "";
$input_gene_list="gene1.1
gene2.1
gene3.1";

// Tools
$max_lookup_input = 10000;
$max_extract_seq_input = 10000;
$max_blast_input = 20;

// BLAST
$blast_example=">protein_or_DNA
ATGAGTTGTGGGGAGGGATTTATGTCACCACAAATAGAGACTAAAGGAAGTGTTGGATTC
AAAGCGGGTGTTAAAGAGTACAAATTGATTTATTATACTCCTGAATACGAAACCAAAGAT
ACCGATATCTTGGTAACATTTCGAGTAACTCCTCAACCTGGAGTTTCGCCTGTAGAAGCA";

?>

