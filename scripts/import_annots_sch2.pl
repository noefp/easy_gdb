#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Term::ReadKey;
use File::Basename;
use lib dirname (__FILE__);
use ImportModule;

# check arguments and print usage
if (scalar(@ARGV) != 4) {
	print "Usage: perl import_annots.pl <annot_file> <annot_src> <species> <annotation_version>\n";
  
  print "\nExample: perl import_annots.pl annotation_example_SwissProt.txt SwissProt \"Mola mola\" \"1.0\" \n";
  
	exit;
}

# save arguments in variables
my ($annot_file, $annot_src, $species_name, $annotation_v) = @ARGV;


my $username="postgres";

print "host name (postgres container name)> ";
my $host=<STDIN>;
print "\n";
chomp($host);


print "DB name> ";
my $dbname=<STDIN>;
print "\n";
chomp($dbname);

print "Password> ";
ReadMode 'noecho';  # Disable echoing
my $password=<STDIN>;
ReadMode 'original';   # Turn it back on

print "\n";
chomp($password);

my $dbh = DBI->connect("dbi:Pg:dbname=$dbname;host=$host;", "$username", "$password");
$dbh->begin_work;

my $species_id = ImportModule::check_species($dbh,$species_name);
my $annot_v_id = ImportModule::check_annotation_version($dbh,$annotation_v);

if (!$species_id || !$annot_v_id) {
  print "Please, run the script import_genes.pl first to import all the genes and their species name and annotation version\n";
  exit;
}

my $annot_src_id = ImportModule::insert_annotation_type($dbh,$annot_src);


# Open annotation file, read line by line and import annotations
open (my $fh2, $annot_file) || die ("\nERROR: the file $annot_file could not be found\n");

while (my $line = <$fh2>) {
  chomp($line);
  
  my ($gene_name,$annot_term,$annot_desc) = split("\t",$line);
  
  if (!$annot_term && !$annot_desc) {
    next;
  }
  
  $gene_name =~ s/^[\s\"]*//;
  $gene_name =~ s/[\s\"]*$//;
  $annot_term =~ s/^[\s\"]*//;
  $annot_term =~ s/[\s\"]*$//;
  $annot_desc =~ s/^[\s\"]*//;
  $annot_desc =~ s/[\s\"]*$//;
  
  # Check gene exists and get gene id in database
  my $gene_id = ImportModule::check_gene($dbh,$gene_name,$species_id,$annot_v_id);
  
  if (!$gene_id) {
    print "The gene $gene_name was not found in the database!\nPlease, consider running the script import_genes.pl first to import all the genes\n";
  }
  
  ImportModule::insert_annot($dbh,$annot_term,$annot_desc,$annot_src_id,$gene_id);
  
} #end of file

$dbh->commit;
$dbh->disconnect;

