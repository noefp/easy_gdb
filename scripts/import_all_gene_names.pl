#!/usr/bin/perl

use strict;
use warnings;

use DBI;
use Term::ReadKey;

# check arguments and print usage
if (scalar(@ARGV) != 2) {
	print "Usage: perl import_all_gene_names.pl <gene_names_file> <annotation_version>\n";
	exit;
}

# save arguments in variables
my ($genes_file,$annotation_version) = @ARGV;

print "host name (postgres container name)> ";
my $host=<STDIN>;
print "\n";
chomp($host);

print "User name> ";
my $username=<STDIN>;
print "\n";
chomp($username);


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

my @genes2;

open (my $fh2, $genes_file) || die ("\nERROR: the file $genes_file could not be found\n");
while (my $gene_name = <$fh2>) {
  chomp($gene_name);
  
	# check if entry already exist and import it to the database
	my $sth2 = $dbh->prepare("SELECT gene_id FROM gene WHERE gene_name = \'$gene_name\'");
	$sth2->execute() or die $sth2->errstr;

	my @gene_id = $sth2->fetchrow_array();

	if (!@gene_id) {
	  $sth2 = $dbh->prepare("INSERT INTO gene (gene_name,gene_version) VALUES (\'$gene_name\',\'$annotation_version\')");
	  $sth2->execute() or die $sth2->errstr;
		$sth2->finish();
	}
    
} #end of file


$dbh->commit;
$dbh->disconnect;
