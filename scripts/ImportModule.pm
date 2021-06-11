package ImportModule;
#ImportModule.pm

use strict;
use warnings;

sub hello_world {
  print "hello world!\n";
}

sub print_sps {
  my $sps = shift;
  
  print "hello $sps!\n";
}

sub check_species {
  my $dbh = shift;
  my $sps_name = shift;
  
  my $my_sps_id;

  # check if entry already exist and import it to the database
  my $sth = $dbh->prepare("SELECT species_id FROM species WHERE species_name = \'$sps_name\'");
  $sth->execute() or die $sth->errstr;

  my @sps_id = $sth->fetchrow_array();

  if (@sps_id) {
    print "\n The species $sps_name already exists in species table: ".$sps_id[0]."\n";
    $my_sps_id = $sps_id[0];
  } 
  else {
    print "\n The species $sps_name was not found in the database\n";
  }

  return $my_sps_id;
}


sub insert_species {
  my $dbh = shift;
  my $sps_name = shift;
  my $jbrowse_folder = shift;
  
  my $my_sps_id;

  my $sth = $dbh->prepare("INSERT INTO species (species_name,jbrowse_folder) VALUES (\'$sps_name\',\'$jbrowse_folder\')");
  $sth->execute() or die $sth->errstr;
  $sth = $dbh->prepare("SELECT species_id FROM species WHERE species_name = \'$sps_name\'");
  $sth->execute() or die $sth->errstr;

  my @sps_id = $sth->fetchrow_array();

  $my_sps_id = $sps_id[0];
  $sth->finish();
  
  print "\n The species $sps_name was inserted in the species table: ".$sps_id[0]."\n";

  return $my_sps_id;
}


sub check_annotation_version {
  my $dbh = shift;
  my $annot_v = shift;
  my $my_annot_v_id;

  # check if entry already exist and import it to the database
  my $sth = $dbh->prepare("SELECT annotation_version_id FROM annotation_version WHERE annotation_version = \'$annot_v\'");
  $sth->execute() or die $sth->errstr;

  my @annot_id = $sth->fetchrow_array();

  if (@annot_id) {
    print "\n The annotation version $annot_v already exists in the annotation_version table: ".$annot_id[0]."\n";
    $my_annot_v_id = $annot_id[0];
  }

  return $my_annot_v_id;
}


sub insert_annotation_version {
  my $dbh = shift;
  my $annot_v = shift;
  my $my_annot_v_id;

  my $sth = $dbh->prepare("INSERT INTO annotation_version (annotation_version) VALUES (\'$annot_v\')");
  $sth->execute() or die $sth->errstr;
  $sth = $dbh->prepare("SELECT annotation_version_id FROM annotation_version WHERE annotation_version = \'$annot_v\'");
  $sth->execute() or die $sth->errstr;

  my @annot_id = $sth->fetchrow_array();

  $my_annot_v_id = $annot_id[0];
  $sth->finish();
  
  print "\n The annotation version $annot_v was inserted in the annotation_version table: ".$annot_id[0]."\n";

  return $my_annot_v_id;
}


sub check_gene {
  my $dbh = shift;
  my $gene_name = shift;
  my $sps_id = shift;
  my $annot_v_id = shift;

  my $my_gene_id;

  # check if entry already exist and import it to the database
  my $sth = $dbh->prepare("SELECT gene_id FROM gene WHERE gene_name = \'$gene_name\' AND species_id = \'$sps_id\' AND annotation_version_id = \'$annot_v_id\'");
  $sth->execute() or die $sth->errstr;

  my @gene_id = $sth->fetchrow_array();

  if (@gene_id) {
    $my_gene_id = $gene_id[0];
  # } else {
    # print "\n$gene_name does not exists in gene table.\nPlease, run the script import_genes.pl to import all gene names before importing annotations\n";
  }

  return $my_gene_id;
}


sub insert_gene {
  my $dbh = shift;
  my $gene_name = shift;
  my $sps_id = shift;
  my $annot_v_id = shift;

  my $my_gene_id;

  my $sth = $dbh->prepare("INSERT INTO gene (gene_name,species_id,annotation_version_id) VALUES (\'$gene_name\',\'$sps_id\',\'$annot_v_id\')");
  $sth->execute() or die $sth->errstr;
  $sth = $dbh->prepare("SELECT gene_id FROM gene WHERE gene_name = \'$gene_name\' AND species_id = \'$sps_id\' AND annotation_version_id = \'$annot_v_id\'");
  $sth->execute() or die $sth->errstr;

  my @gene_id = $sth->fetchrow_array();

  $my_gene_id = $gene_id[0];
  $sth->finish();

  return $my_gene_id;
}


sub insert_annotation_type {
  my $dbh = shift;
  my $annotation_type = shift;

  my $my_annotation_type_id;

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
  
  return $my_annotation_type_id;
}

sub insert_annot {
  my $dbh = shift;
  my $annotation_term = shift;
  my $annotation_desc = shift;
  my $annotation_type_id = shift;
  my $gene_id = shift;

  my $my_annot_id;
  # my $my_annotation_type_id;

  # to remove special character that crush the SQL import code
  if ($annotation_desc) {
    $annotation_desc =~ s/[\'\"]//g;
  }

  # if ($annotation_type eq "TAIR") {
  #   $annotation_term =~ s/ARATHwo_//;
  # }
  # if ($annotation_type eq "TAIR") {
  #   $annotation_desc =~ s/Has \d+ Blast hits to \d+ proteins in \d+ species:.+\(source: NCBI BLink\)\.//;
  # }
  # if ($annotation_type eq "NCBI Nr") {
  #   $annotation_desc =~ s/^PREDICTED: //;
  # }

  # check if entry already exist and import it to the database
  my $sth2 = $dbh->prepare("SELECT annotation_id FROM annotation WHERE annotation_term = \'$annotation_term\' AND annotation_type_id = \'$annotation_type_id\'");
  $sth2->execute() or die $sth2->errstr;

  my @annot_id = $sth2->fetchrow_array();
  my @ident_annot_id;
  
  if (@annot_id) {
    print "\n $annotation_term already exists in gene table: ".$annot_id[0]."\n";
    $my_annot_id = $annot_id[0];
    
    # check identical description already exist
    my $sth3 = $dbh->prepare("SELECT annotation_id FROM annotation WHERE annotation_desc = \'$annotation_desc\' AND annotation_term = \'$annotation_term\' AND annotation_type_id = \'$annotation_type_id\'");
    $sth3->execute() or die $sth3->errstr;
    
    @ident_annot_id = $sth3->fetchrow_array();
    $sth3->finish();
  }
  
  if ($my_annot_id && !@ident_annot_id) {
    
    print "UPDATING: $annotation_term\t$annotation_desc\n";

    $sth2 = $dbh->prepare("UPDATE annotation SET (annotation_term,annotation_desc,annotation_type_id) = (\'$annotation_term\',\'$annotation_desc\',\'$annotation_type_id\') WHERE annotation_id = \'$my_annot_id\'");
    $sth2->execute() or die $sth2->errstr;
    $sth2->finish();
  }
  
  
  if (!$my_annot_id) {

    $sth2 = $dbh->prepare("INSERT INTO annotation (annotation_term,annotation_desc,annotation_type_id) VALUES (\'$annotation_term\',\'$annotation_desc\',\'$annotation_type_id\')");
    $sth2->execute() or die $sth2->errstr;
    $sth2 = $dbh->prepare("SELECT annotation_id FROM annotation WHERE annotation_term = \'$annotation_term\'");
    $sth2->execute() or die $sth2->errstr;

    my @annot_id = $sth2->fetchrow_array();

    $my_annot_id = $annot_id[0];
    $sth2->finish();
    
    print "INSERTING: $annotation_term\t$annotation_desc\n";
  }
  

  # check if entry already exist and import it to the database
  my $sth3 = $dbh->prepare("SELECT gene_annotation_id FROM gene_annotation WHERE gene_id = '".$gene_id."' AND annotation_id = '".$my_annot_id."'");
  $sth3->execute() or die $sth3->errstr;

  my ($gene_annotation_id) = $sth3->fetchrow_array();

  if (!$gene_annotation_id) {
    $sth3 = $dbh->prepare("INSERT INTO gene_annotation (gene_id,annotation_id) VALUES ('".$gene_id."','".$my_annot_id."')");
    $sth3->execute() or die $sth3->errstr;
    $sth3->finish();
  }

}



1;