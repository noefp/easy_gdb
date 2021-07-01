# Easy GDB

Welcome to easy GDB, this tool will help you to create your own genomic database with tools such as BLAST, Genome browser (JBrowse),
file downloads, sequence extraction, annotation search, bulk annotation extraction and gene list lookup.

##  Installation

Easy GDB requires PHP and PostgreSQL to run.

It should be easy to install in a linux computer, such as the ones usually provided in servers to hosts genomic database applications.
To use it in Mac or Windows it would be recommendable to use Docker or VirtualBox to run it in a linux container ir virtual machine.

In most of the servers is probable that some of the tools needed are already installed, and you work often with linux you wuold probably have some of them too.

#### Install Git and PHP

Lets install git to download the easy GDB code and PHP to be able to run the web.
```bash
sudo apt-get update
apt-get install git
apt-get install php
```
#### Set up server
In a server (not mandatory for local instalations) you would need to use Apache or Nginx webservers to host your application in a server.

```bash
sudo apt-get install apache2

cd /etc/apache2/
sudo cp 000-default.conf easy_gdb.conf
sudo a2dissite 000-default.conf
sudo a2ensite easy_gdb.conf
systemctl reload apache2
```


#### Install BLAST
```bash
apt-get install ncbi-blast+
```


#### Install PostgreSQL 

To install Postgres you can follow the instructions at:
https://www.postgresql.org/download/linux/ubuntu/

The next commands worked well at the time this documentation was writen:
```bash
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
sudo apt-get update
sudo apt-get -y install postgresql-10

sudo apt-get install libpq-dev
sudo apt-get install php7.2-pgsql
```
##### In server
```bash
    sudo service apache2 restart
```

##### locally, stop local server and run again

```bash
php -S localhost:8000
```

##### Set up easy_gdb database
open the file `example_db/egdb_files/egdb_template_conf/database_access.php`

```php
function getConnectionString(){return "host=localhost dbname=annot1 user=web_usr password=password";};
```

##### Set up password

```bash
sudo -u postgres psql postgres
```

```sql
\du
\password postgres
```

##### create new role with temporal password
```sql
CREATE ROLE web_usr WITH LOGIN ENCRYPTED PASSWORD 'tmp_password' CREATEDB;
CREATE DATABASE annot1;
\l
\q
```

##### Change role password
##### Connect to postgres console and change password (not visible)
```bash
    psql -h localhost -U postgres
```
```sql
\password web_usr
type a new password
\q
```

##### Import annotation schema to database
```bash
example_db$ psql –U postgres –d annot1 –h localhost –a –f easy_gdb/scripts/create_tea_schema2.sql
```

#### Install Perl dependencies
    apt-get install cpanminus
    cpanm -L ~/local-lib/ DBI
    cpanm -L ~/local-lib/ Term::ReadKey
    cpanm -L ~/local-lib/ DBD::Pg


#### load local-lib in Perl5lib
    apt-get install vim
    vim ~/.bashrc

Add the line below at the end of the file. Remember to change your user name.

    export PERL5LIB=/home/your_username/local-lib/lib/perl5:$PERL5LIB
    source ~/.bashrc

#### Import annotations
    perl easy_gdb/scripts/import_genes.pl easy_gdb/templates/anotations/gene_list.txt "Homo sapiens" "v1.0" "easy_gdb_sample"
    perl easy_gdb/scripts/import_annots_sch2.pl easy_gdb/templates/anotations/annotation_example_SwissProt.txt SwissProt "Homo sapiens" "v1.0"
    perl easy_gdb/scripts/import_annots_sch2.pl easy_gdb/templates/anotations/annotation_example_TAIR10.txt TAIR10 "Homo sapiens" "v1.0"

You can add custom annotation links in the annotation_links.json file:
`example_db/egdb_files/egdb_template_conf/annotation_links.json`

This file includes example links for TAIR10, Araport11, SwissProt, InterPro and NCBI. 
The name used (TAIR10, Araport11, SwissProt ...) should be used in the import_annots_sch2.pl script, as shown above
in the link query_id will be replaced by the gene name or annotation term.



## Set up easy GDB using the template example

    mkdir example_db
    cd example_db


    git clone https://github.com/noefp/easy_gdb.git


    php -S localhost:8000

in web browser `localhost:8000/easy_gdb/`


![easy GDB home](templates/egdb_img_samples/easy_gdb_home.png)

    example_db$ mkdir egdb_files
    example_db$ cd egdb_files
    egdb_files$ cp -r ../easy_gdb/templates/egdb_template_conf .
    egdb_files$ cp -r ../easy_gdb/templates/egdb_img_samples .

open `example_db/easy_gdb/configuration_path.php` and set the configuration path
path to `example_db/egdb_files/egdb_template_conf`

open `example_db/example_db_files/egdb_template_conf` and set the root path where the `easy_db` folder is
Usually in a server you could the files in `/var/www/html`
Locally you could have them in `/home/your_user_name/Desktop/example_db`

Reload the web browser `localhost:8000/easy_gdb/` and you should see an empty implementation

    cp -r ../easy_gdb/templates/egdb_custom_text .

Reload again and now you should be able to see text examples for the home and about us page

Open again the configuration file `example_db/easy_gdb/configuration_path.php`

Here you can customize your site. 
If you used the folder name `egdb_files` all path should work when you place the files there.
If you want use a different name remember to change the name in the file paths

For example, for development you could have multiple sites or multiple versions. 
You could easily change between them having different file folders and just changing the path to the active one in `easy_gdb/configuration_path.php`

You can customize the header variables `$dbTitle`, `$header_img` and `$db_logo` to change the site title and header and logo images
Try to change them and reload `localhost:8000/easy_gdb/index.php` to see the cahnges


#### Customize logos

In `egdb_files/egdb_img_samples/logos/` you can place logo images, and you can use the file `logos.json` to customize size and link


#### Customize the toolbar

Below, in the toolbar variables, you can customize wich links will be displayed in the toolbar.
a value `1` enable the link and `0` disable it. Choose the links you want to show or hide.

Lets take a look to each one of the links.

##### Home page

The home page is always available. If you copied the `egdb_custom_text` folder, 
then you should be able to see the example text for the welcome page. 
You can open the file `example_db/egdb_files/egdb_custom_text/welcome_text.php` in a text editor to customize the content.

##### About Us

If the variable `$tb_about` is equal `1` in the configuration file (`easyGDB_conf.php`) 
and you copied the `egdb_custom_text` folder, then you should be able to see example text for the `About Us` section.

You can open the file `example_db/egdb_files/egdb_custom_text/about.php` in a text editor to customize the content.
Additionally, you can set the about variables `$ab_citation` to `0` or `1` to display, or not, 
the citation of the papers where the genomic database or data where published. You can add the citation in `db_citation.php`

You can also add here information about the participant labs. 
For this, the `$ab_labs` should be enabled (equal to `1`) and we need to create the `egdb_labs` folder.

    example_db/egdb_files$ cp -r ../easy_gdb/templates/egdb_labs .

Reload the page `localhost:8000/easy_gdb/about.php` to see the example.
You can create a json file for each lab or you can copy and modify the provided examples to add your own information.
There, you can include the lab name and, for each person, you can include name, position, a picture (placed in `egdb_files/people/`), link to a personal page, 
and, in the more_info array you could add data such as phone, email, and any other custom information.

##### Species

If you want to host data for multiple species or accessions you shold enable the variable `$tb_species = 1`.
If not you can disable it setting it to `$tb_species = 0`.

Copy the species example file in the `egdb_files` folder to see some examples:

    example_db/egdb_files$ cp -r ../easy_gdb/templates/egdb_species .

Then, you will be able to see them at `localhost:8000/easy_gdb/species.php`

To customize the list of species, use the file `species_list.json`.
There you can include species name, common name, image and link to a 
descriptive custom PHP file (`human.php`, `species1.php` and `species2.php`) where you can write all the information about the species.


##### Downloads

You can create a download folder where you can place the files you want to provide for downloading.
The content of that folder will be read and presented in the web site. 
It is recommended to compress the files before place them there for sharing.
For an example, copy the provided download folder (note I copied this one in `example_db`, not inside the `egdb_files`).

    example_db$ cp -r easy_gdb/templates/download .

Then, you will be able to see them at `localhost:8000/easy_gdb/downloads.php`

You can create your own structure of files and folders and place them in the downloads folder.

##### Tools

It is possible to enable or disable the tools `Search page`, `Search box` in toolbar, `BLAST`, `Genome browser`, `Sequence extraction`, `Annotation extraction`, and `Gene version lookup`.
Turn the variables to `1` or to `0` to enable or disable them.

##### Search

To enable the search and the toolbar search box first we must install the PostgreAQL database and import the annotations.

##### BLAST

In the web browser, follow the link to `BLAST` in the tools toolbar menu. There you will see the BLAST input page.
In the `easyGDB_conf.php`, you can change the input example sequence changing the variable `$blast_example`.

To add BLAST datasets first we need to create a `blast_dbs` folder (path can be set up in `easyGDB_conf.php`) and place there the blast databases.

    example_db$ mkdir blast_dbs

Then we can copy our fasta files there and create the blast databases:

    cd blast_dbs
    example_db/blast_dbs$ cp ../easy_gdb/templates/blast/*fasta .
    makeblastdb -in sample_blast_DB_proteins.fasta -dbtype 'prot' -parse_seqids
    makeblastdb -in sample_blast_DB_nucleotides.fasta -dbtype 'nucl' -parse_seqids

The variable `$max_blast_input` (in `easyGDB_conf.php`) controls the maximum number of sequences allowed as input in `BLAST`.


##### Sequence extraction

This tool uses the datasets in the `blast_dbs` folder to extract the sequences from a list of genes.

If you created the folder `blast_dbs` and added the sequence blast databases (explained above), then the tool should be ready to use.
You can modify the example input gene list changing the variable `$input_gene_list`.

The variable `$max_extract_seq_input` (in `easyGDB_conf.php`) controls the maximum number of input gene names to extract.

##### Genome browser:

See `Install and set up JBrowse` Below.

##### Annotation extraction:

To enable the annotation extraction first we must install the PostgreSQL database and import the annotations.

##### Gene version lookup: 

    example_db$ cp -r easy_gdb/templates/lookup .

The variable `$max_lookup_input` (in `easyGDB_conf.php`) controls the maximum number of gene names allowed as input.


#### Install and set up JBrowse

    jbrowse$ bin/prepare-refseqs.pl --fasta ../easy_gdb/templates/jbrowse/genome.fasta --out data/easy_gdb_sample
    jbrowse$ bin/flatfile-to-json.pl -gff ../easy_gdb/templates/jbrowse/gene_models.gff --key "EasyGDB gene models" --trackLabel egdb_gene_models --trackType CanvasFeatures --type mRNA --out data/easy_gdb_sample
    jbrowse$ bin/generate-names.pl --tracks egdb_gene_models --out data/easy_gdb_sample/

data/easy_gdb_sample/trackList.json

```json
    {
       "category" : "02 Annotations",
       "compress" : 0,
       "key" : "EasyGDB gene models",
       "label" : "egdb_gene_models",
       "onClick" : {
          "action" : "newWindow",
          "label" : "Go to gene view",
          "url" : "/easy_gdb/gene.php?name={id}"
       },
       "storeClass" : "JBrowse/Store/SeqFeature/NCList",
       "style" : {
          "className" : "feature"
       },
       "trackType" : "CanvasFeatures",
       "type" : "CanvasFeatures",
       "urlTemplate" : "tracks/egdb_gene_models/{refseq}/trackData.json"
    }
```

data/easy_gdb_sample/tracks.conf

    [general]
    dataset_id = easy_gdb_sample


jbrowse/jbrowse.conf

    [datasets.easyGDB]
    url  = ?data=data/easy_gdb_sample
    name = Easy GDB Example

    [datasets.volvox]
    url  = ?data=sample_data/json/volvox
    name = Volvox Example

    [datasets.yeast]
    url  = ?data=sample_data/json/yeast
    name = Yeast Example




