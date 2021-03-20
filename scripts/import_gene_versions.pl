#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Term::ReadKey;

# check arguments and print usage
if (scalar(@ARGV) != 3) {
	print "Usage: perl import_gene_version.pl <gene_version_file> <gene_version1> <gene_version2>\n";
	exit;
}

# save arguments in variables
my ($gene_lookup_file,$gene_version1,$gene_version2) = @ARGV;


sub insert_gene {
	my $dbh = shift;
	my $gene_name = shift;
	my $gene_version = shift;
	my $my_gene_version_id;
	my $my_gene_id;


	# check if gene version already exist and import it to the database
	my $sth = $dbh->prepare("SELECT gene_version_id FROM gene_version WHERE gene_version = \'$gene_version\'");
	$sth->execute() or die $sth->errstr;

	my @gene_version_id = $sth->fetchrow_array();

	if (@gene_version_id) {
	  #print "\n $gene_name already exists in gene table: ".$gene_id[0]."\n";
		$my_gene_version_id = $gene_version_id[0];
	} else {
		# print "$gene_name\tV$gene_version\n";

	  $sth = $dbh->prepare("INSERT INTO gene_version (gene_version) VALUES (\'$gene_version\')");
	  $sth->execute() or die $sth->errstr;
		$sth = $dbh->prepare("SELECT gene_version_id FROM gene_version WHERE gene_version = \'$gene_version\'");
		$sth->execute() or die $sth->errstr;

		(my @gene_version_id) = $sth->fetchrow_array();

		# print $gene_id[0]."\n";
		$my_gene_version_id = $gene_version_id[0];
		$sth->finish();
	}


	# check if entry already exist and import it to the database
	my $sth2 = $dbh->prepare("SELECT gene_id FROM gene WHERE gene_name = \'$gene_name\'");
	$sth2->execute() or die $sth2->errstr;

	my @gene_id = $sth2->fetchrow_array();

	if (@gene_id) {
	  #print "\n $gene_name already exists in gene table: ".$gene_id[0]."\n";
		$my_gene_id = $gene_id[0];
	} else {
		# print "$gene_name\tV$gene_version\n";

	  $sth2 = $dbh->prepare("INSERT INTO gene (gene_name,gene_version_id) VALUES (\'$gene_name\',\'$my_gene_version_id\')");
	  $sth2->execute() or die $sth2->errstr;
		$sth2 = $dbh->prepare("SELECT gene_id FROM gene WHERE gene_name = \'$gene_name\'");
		$sth2->execute() or die $sth2->errstr;

		(my @gene_id) = $sth2->fetchrow_array();

		# print $gene_id[0]."\n";
		$my_gene_id = $gene_id[0];
		$sth2->finish();
	}

  return $my_gene_id;
}






sub insert_gene_gene {
	my $dbh = shift;
	my $gene_id1 = shift;
	my $gene_id2 = shift;

	# check if entry already exist and import it to the database
	my $sth = $dbh->prepare("SELECT gene_gene_id FROM gene_gene WHERE gene_id1 = '".$gene_id1."' AND gene_id2 = '".$gene_id2."'");
	$sth->execute() or die $sth->errstr;

	my ($gene_gene_id) = $sth->fetchrow_array();

	if ($gene_gene_id) {
	 # print "\n $gene_id1-$gene_id2 already exists in gene_gene table: $gene_gene_id\n";
	} else {
	  $sth = $dbh->prepare("INSERT INTO gene_gene (gene_id1,gene_id2) VALUES ('".$gene_id1."','".$gene_id2."')");
	  $sth->execute() or die $sth->errstr;
		$sth->finish();
	}
}


my $host="docker_test2_postgres_1";
my $username="postgres";
my $dbname="annot1";

# print "host name (postgres container name)> ";
# my $host=<STDIN>;
# print "\n";
# chomp($host);
#
#
# print "DB name> ";
# my $dbname=<STDIN>;
# print "\n";
# chomp($dbname);

print "Password> ";
ReadMode 'noecho';  # Disable echoing
my $password=<STDIN>;
ReadMode 'original';   # Turn it back on

print "\n";
chomp($password);

my $dbh = DBI->connect("dbi:Pg:dbname=$dbname;host=$host;", "$username", "$password");
$dbh->begin_work;

my @genes2;

open (my $fh2, $gene_lookup_file) || die ("\nERROR: the file $gene_lookup_file could not be found\n");
while (my $line = <$fh2>) {
  chomp($line);
  my ($gene_name1,$gene_name2) = split("\t",$line);

	if ($gene_name2) {
	  @genes2 = split("\;",$gene_name2);
	}

	my $gene_id1 = insert_gene($dbh,$gene_name1,$gene_version1);

	foreach my $g2 (@genes2) {
    my $gene_id2 = insert_gene($dbh,$g2,$gene_version2);
    insert_gene_gene($dbh,$gene_id1,$gene_id2);
	}
  

  # my $gene_id2 = insert_gene($dbh,$gene_name2,$gene_version2);

  # insert_gene_gene($dbh,$gene_id1,$gene_id2);
  
} #end of file




$dbh->commit;
$dbh->disconnect;
