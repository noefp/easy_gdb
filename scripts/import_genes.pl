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
  print "Usage: perl import_genes.pl <gene_list.txt> <species> <annotation_version> <JBrowse_folder>\n\n";
  print "Example: perl import_genes.pl gene_list.txt \"Mola mola\" \"1.0\" \"easy_gdb_sample\" \n";
  exit;
}

# save arguments in variables
my ($gene_file,$species,$annotation_v,$jbrowse_folder) = @ARGV;

# ImportModule::hello_world();
# ImportModule::print_sps($species);

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

my $species_id = ImportModule::check_species($dbh,$species);
if (!$species_id) {
  $species_id = ImportModule::insert_species($dbh,$species,$jbrowse_folder);
}

my $annot_v_id = ImportModule::check_annotation_version($dbh,$annotation_v);
if (!$annot_v_id) {
  $annot_v_id = ImportModule::insert_annotation_version($dbh,$annotation_v);
}

open (my $fh2, $gene_file) || die ("\nERROR: the file $gene_file could not be found\n");

while (my $line = <$fh2>) {
  chomp($line);
  
  my $gene_name = $line;
  my $gene_id = ImportModule::check_gene($dbh,$gene_name,$species_id,$annot_v_id);
  
  if (!$gene_id) {
    ImportModule::insert_gene($dbh,$gene_name,$species_id,$annot_v_id);
  }
  
} #end of file

$dbh->commit;
$dbh->disconnect;
