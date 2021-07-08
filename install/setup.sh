mkdir ../../egdb_files;
cp -r ../templates/downloads ../../;
cp -r ../templates/lookup ../../;
cp -r ../templates/annotations ../../egdb_files/;
cp -r ../templates/egdb_custom_text ../../egdb_files/;
cp -r ../templates/egdb_img_samples ../../egdb_files/;
cp -r ../templates/egdb_labs ../../egdb_files/;
cp -r ../templates/egdb_species ../../egdb_files/;
cp -r ../templates/egdb_template_conf ../../egdb_files/;
mkdir ../../blast_dbs;
cp ../templates/blast/*fasta ../../blast_dbs/;
cd ../../blast_dbs;
makeblastdb -in sample_blast_DB_proteins.fasta -dbtype 'prot' -parse_seqids;
makeblastdb -in sample_blast_DB_nucleotides.fasta -dbtype 'nucl' -parse_seqids;
cd ../
wget https://github.com/GMOD/jbrowse/releases/download/1.16.11-release/JBrowse-1.16.11.zip
unzip JBrowse-1.16.11.zip
mv JBrowse-1.16.11/ jbrowse/
cd jbrowse/
./setup.sh