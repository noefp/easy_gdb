mkdir ../../egdb_files;
cp -r ../templates/downloads ../../;
cp -r ../templates/lookup ../../;
cp -r ../templates/annotations ../../egdb_files/;
cp -r ../templates/egdb_custom_text ../../egdb_files/;
cp -r ../templates/egdb_images ../../egdb_files/;
cp -r ../templates/egdb_labs ../../egdb_files/;
cp -r ../templates/egdb_species ../../egdb_files/;
cp -r ../templates/expression_data ../../;
cp -r ../templates/egdb_conf ../../egdb_files/;
mkdir ../../blast_dbs;
cp -r ../templates/blast/* ../../blast_dbs/;
cd ../../blast_dbs/category_1;
makeblastdb -in sample_blast_DB_proteins.fasta -dbtype 'prot' -parse_seqids;
makeblastdb -in sample_blast_DB_nucleotides.fasta -dbtype 'nucl' -parse_seqids;
cd ../category_2;
makeblastdb -in sample_blast_DB_genome.fasta -dbtype 'nucl' -parse_seqids;
cd ../../
wget https://github.com/GMOD/jbrowse/releases/download/1.16.11-release/JBrowse-1.16.11.zip
unzip JBrowse-1.16.11.zip
mv JBrowse-1.16.11/ jbrowse/
cd jbrowse/
./setup.sh
bin/prepare-refseqs.pl --fasta ../easy_gdb/templates/jbrowse/genome.fasta --out data/easy_gdb_sample
bin/flatfile-to-json.pl -gff ../easy_gdb/templates/jbrowse/gene_models.gff --key "EasyGDB gene models" --trackLabel egdb_gene_models --trackType CanvasFeatures --type mRNA --out data/easy_gdb_sample
bin/generate-names.pl --tracks egdb_gene_models --out data/easy_gdb_sample/
cp ../easy_gdb/templates/jbrowse/jbrowse.conf .
cp ../easy_gdb/templates/jbrowse/tracks.conf data/easy_gdb_sample/
cp ../easy_gdb/templates/jbrowse/trackList.json data/easy_gdb_sample/