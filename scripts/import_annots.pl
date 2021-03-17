#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Term::ReadKey;

# check arguments and print usage
if (scalar(@ARGV) != 3) {
	print "Usage: perl import_annots.pl <annot_file> <annot_src> <gene_version>\n";
	exit;
}

# save arguments in variables
my ($annot_file, $annot_src, $gene_version) = @ARGV;


sub insert_gene {
	my $dbh = shift;
	my $gene_name = shift;
	my $gene_version = shift;
	my $my_gene_id;

	# check if entry already exist and import it to the database
	my $sth = $dbh->prepare("SELECT gene_id FROM gene WHERE gene_name = \'$gene_name\'");
	$sth->execute() or die $sth->errstr;

	my @gene_id = $sth->fetchrow_array();

	if (@gene_id) {
	  #print "\n $gene_name already exists in gene table: ".$gene_id[0]."\n";
		$my_gene_id = $gene_id[0];
	} else {
		# print "$gene_name\tV$gene_version\n";

	  $sth = $dbh->prepare("INSERT INTO gene (gene_name,genome_version) VALUES (\'$gene_name\',\'$gene_version\')");
	  $sth->execute() or die $sth->errstr;
		$sth = $dbh->prepare("SELECT gene_id FROM gene WHERE gene_name = \'$gene_name\'");
		$sth->execute() or die $sth->errstr;

		(my @gene_id) = $sth->fetchrow_array();

		# print $gene_id[0]."\n";
		$my_gene_id = $gene_id[0];
		$sth->finish();
	}

  return $my_gene_id;
}


sub insert_annot {
	my $dbh = shift;
	my $annotation_term = shift;
	my $annotation_desc = shift;
	my $annotation_type = shift;
	my $gene_id = shift;

	my $my_annot_id;
	my $my_annotation_type_id;

	# to remove special character that crush the SQL import code
	if ($annotation_desc) {
		$annotation_desc =~ s/[\'\"]//g;
	}

	if ($annotation_type eq "TAIR") {
		$annotation_term =~ s/ARATHwo_//;
	}
	if ($annotation_type eq "TAIR") {
		$annotation_desc =~ s/Has \d+ Blast hits to \d+ proteins in \d+ species:.+\(source: NCBI BLink\)\.//;
	}
	if ($annotation_type eq "NCBI Nr") {
		$annotation_desc =~ s/^PREDICTED: //;
	}

	# check if annotation type already exist and import it to the database
	my $sth = $dbh->prepare("SELECT annotation_type_id FROM annotation_type WHERE annotation_type = \'$annotation_type\'");
	$sth->execute() or die $sth->errstr;
  
	my @annotation_type_id = $sth->fetchrow_array();

	if (@annotation_type_id) {
		#print "\n $gene_id-$my_annot_id already exists in gene_annotation table: gene_annotation_id\n";
    $my_annotation_type_id = $annotation_type_id[0];
	} else {
		$sth = $dbh->prepare("INSERT INTO annotation_type (annotation_type) VALUES ('".$annotation_type."')");
		$sth->execute() or die $sth->errstr;
		$sth = $dbh->prepare("SELECT annotation_type_id FROM annotation_type WHERE annotation_type = \'$annotation_type\'");
		$sth->execute() or die $sth->errstr;

		(my @annotation_type_id) = $sth->fetchrow_array();

		# print $gene_id[0]."\n";
		$my_annotation_type_id = $annotation_type_id[0];
    
		$sth->finish();
	}
  
  
  
	# check if entry already exist and import it to the database
	my $sth2 = $dbh->prepare("SELECT annotation_id FROM annotation WHERE annot_term = \'$annotation_term\'");
	$sth2->execute() or die $sth2->errstr;

	my @annot_id = $sth2->fetchrow_array();

	if (@annot_id) {
		#print "\n $annotation_term already exists in gene table: ".$annot_id[0]."\n";
		$my_annot_id = $annot_id[0];
	} else {
		# print "$annotation_term\t$annotation_desc\n";

		$sth2 = $dbh->prepare("INSERT INTO annotation (annot_term,annot_desc,annotation_type_id) VALUES (\'$annotation_term\',\'$annotation_desc\',\'$my_annotation_type_id\')");
		$sth2->execute() or die $sth2->errstr;
		$sth2 = $dbh->prepare("SELECT annotation_id FROM annotation WHERE annot_term = \'$annotation_term\'");
		$sth2->execute() or die $sth2->errstr;

		(my @annot_id) = $sth2->fetchrow_array();

		# print $gene_id[0]."\n";
		$my_annot_id = $annot_id[0];
		$sth2->finish();
	}

	# check if entry already exist and import it to the database
	my $sth3 = $dbh->prepare("SELECT gene_annotation_id FROM gene_annotation WHERE gene_id = '".$gene_id."' AND annotation_id = '".$my_annot_id."'");
	$sth3->execute() or die $sth3->errstr;

	my ($gene_annotation_id) = $sth3->fetchrow_array();

	if ($gene_annotation_id) {
		#print "\n $gene_id-$my_annot_id already exists in gene_annotation table: gene_annotation_id\n";
	} else {
		$sth3 = $dbh->prepare("INSERT INTO gene_annotation (gene_id,annotation_id) VALUES ('".$gene_id."','".$my_annot_id."')");
		$sth3->execute() or die $sth3->errstr;
		$sth3->finish();
	}

}


my $host="localhost";
my $username="postgres";

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


open (my $fh2, $annot_file) || die ("\nERROR: the file $annot_file could not be found\n");
while (my $line = <$fh2>) {
  chomp($line);
  my ($gene_name,$annot_term,$annot_desc) = split("\t",$line);


	my $gene_id = insert_gene($dbh,$gene_name,$gene_version);

	insert_annot($dbh,$annot_term,$annot_desc,$annot_src,$gene_id);

} #end of file




$dbh->commit;
$dbh->disconnect;
