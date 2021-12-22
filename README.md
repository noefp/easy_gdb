# Easy GDB

Welcome to easy GDB. 
This tool will help you to create your own genomic database with tools such as BLAST, 
Genome browser (JBrowse), file downloads, sequence extraction, annotation search, 
bulk annotation extraction and gene list lookup.

##  Installation

Easy GDB requires PHP and PostgreSQL to run. 
You can use Docker (https://docs.docker.com/get-docker/) to install our easy GDB container or follow the steps at the bottom to install easy GDB from scratch in a Linux system (https://github.com/noefp/easy_gdb#instalation-in-linux-system-without-docker).

It should be easy to install it in a linux computer, such as the ones usually provided in servers to hosts genomic database applications.
To use it in Mac or Windows it would be recommendable to use the Docker container or VirtualBox to run it in a linux virtual machine.

In most of the servers is probable that some of the tools needed are already installed, 
and if you work often with linux you would probably have some of them already.

### Set up easy GDB using the template example and Docker

Using the Docker, the first step will be cloning the docker-compose repository. 
Go to the path where you want to save your genomics database and clone the easyGDB_docker repository from GitHub:


    git clone https://github.com/noefp/easyGDB_docker.git


Go into the easyGDB_docker folder

    cd easyGDB_docker


Then, build the container:

    easyGDB_docker$ docker-compose build

and start the container using `docker-compose` or the Docker desktop application:

    easyGDB_docker$ docker-compose up


Using the Docker container we install easy GDB at `/var/www/html/` (`src/` in the Docker container).
Open the easy_GDB Docker container terminal or the Docker desktop application.

    easyGDB_docker$ docker-compose exec easy_gdb /bin/bash

Clone the easy_GBD code from Github:

    git clone https://github.com/noefp/easy_gdb.git

Now, we will create the configuration and example folder structure.
Go to `easy_gdb/install` and run the `setup.sh` script:

    cd easy_gdb/install/
    bash setup.sh

When running the easy GDB setup, installing JBrowse Perl pre-requisites might take some minutes.
Please be patient.

When the setup finishes, this should create some folders, subfolders and files at the same level as easy_gdb.
You can take a look using your file browser at `src` or in the terminal using the commands below.

    ls -lh /var/www/html

You should be able to see the folders `blast_dbs`, `downloads`, `easy_gdb`, `egdb_files`, `jbrowse` and `lookup`, 
and inside them there are some example templates to help you customize your own genomic web portal.

At this moment most of the features of easy_gdb should be alredy available (all but the parts depending on the annotation database).


In web browser (Chrome, Firefox, etc) go to: `localhost:8000/easy_gdb/`

You should be able to see an example of easy_gdb running.

![easy GDB home](templates/egdb_images/easy_gdb_home.png)


> In case of installing easy GDB in a Linux system, not using Docker, run the next command to start a local PHP server:
>
>    example_db$ php -S localhost:8000


#### Set up easy_gdb database

We need to set up the database so the easy GDB code is able to find it. 
Remember to change the password by the password you will use for web_usr below (https://github.com/noefp/easy_gdb#create-a-new-role-db-user)

open the file `egdb_files/egdb_conf/database_access.php`.

```php
function getConnectionString(){return "host=DB dbname=annot1 user=web_usr password=password";};
```

> If not using the Docker container the host for the postgreSQL database is usually `localhost`

##### Set up password

[in the Docker container you already have a postgres password defined]

Open a terminal using docker-compose, docker exec or Docker desktop

    docker-compose exec DB /bin/bash
    psql -U postgres

or

    docker exec -ti DB psql -U postgres
    
You can use `\q` to exit the PostgreSQL console or exit to leave the Docker bash console.

To change the password for the postgres user:
```sql
\password postgres
You will be asked to type your new password
\q
```

##### Create a new database

Here, we will create a new database `annot1`. Any time you want to create a new database to test some data or new versions, 
you can create a new one nad point to it in the file `egdb_files/egdb_conf/database_access.php`.

Open a terminal using docker-compose, docker exec or Docker desktop if you needto and create a new database:

```sql
CREATE DATABASE annot1;
\l
\q
```

##### Create a new role (DB user)

It is recommended to use a different user than postgres to access the database (it will have limited control).
Here, we will create the user `web_usr`. Note that in this example the password you type will be visible in the terminal,
and the history, so we will create a temporal password and then we will change it in the next step.

Open a terminal using docker-compose, docker exec or Docker desktop if you needto and create a new database:

```sql
CREATE ROLE web_usr WITH LOGIN ENCRYPTED PASSWORD 'tmp_password' CREATEDB;
\password web_usr
type a new password
\q
```

##### Import annotation schema to database

Now we should have an empty database called `annot1` created.
In this step we will create the database schema:

    docker exec -i DB psql --username postgres annot1 < src/easy_gdb/scripts/create_annot_schema2.sql


#### Import annotations

Here, we will learn how to import annotations to the database.
First we will import all the gene names, for that we will need a file such as
`easy_gdb/templates/anotations/gene_list.txt` with all the gene identifiers from our organism. 
It is recommended to use the transcript name (gene1.1).

We will import all the gene names using the script `import_genes.pl` and we will provide the gene list file, 
species name, gene annotation version, and folder name for JBrowse (remember this name to use it when you set up JBrowse).
This way we can link the genes with the genome browser.

Open a terminal using docker-compose or Docker desktop

    docker-compose exec easy_gdb /bin/bash

    perl easy_gdb/scripts/import_genes.pl egdb_files/annotations/gene_list.txt "Homo sapiens" "1.0" "easy_gdb_sample"

It will ask for the host name (`DB`), DB name (`annot1`), and the postgres password.

Now we will add annotations to the genes using the script `import_annots_sch2.pl`. 
For that, we will need a file such as `annotation_example_SwissProt.txt`, 
where we have the first column with the gene name, the second column with the annotation term 
(ID for SwissProt, or a close related model species, GO term, InterProscan term, EC, KEGG, etc.),
and a third column with the annotation description. 
As an example we will import annotations for SwissProt and TAIR10 (for model plant arabidopsis).
The script needs the annotations file, name of the annotation (SwissProt, TAIR10, etc.), species name and annotation version.

    perl easy_gdb/scripts/import_annots_sch2.pl egdb_files/annotations/annotation_example_SwissProt.txt SwissProt "Homo sapiens" "1.0"

    perl easy_gdb/scripts/import_annots_sch2.pl egdb_files/annotations/annotation_example_TAIR10.txt TAIR10 "Homo sapiens" "1.0"

You can add custom annotation links in the annotation_links.json file:
`egdb_files/annotations/annotation_links.json`

```json
{
  "TAIR10":"http://www.arabidopsis.org/servlets/TairObject?type=locus&name=query_id",
  "Araport11":"http://www.arabidopsis.org/servlets/TairObject?type=locus&name=query_id",
  "SwissProt":"http://www.uniprot.org/uniprot/query_id",
  "InterPro":"https://www.ebi.ac.uk/interpro/entry/InterPro/query_id",
  "NCBI":"https://www.ncbi.nlm.nih.gov/protein/query_id"
}
```

This file includes example links for TAIR10, Araport11, SwissProt, InterPro and NCBI. 
The name used (TAIR10, Araport11, SwissProt ...) should be used in the import_annots_sch2.pl script, as shown above.
In the link, `query_id` will be replaced by the gene id or annotation term.


#### Customize file paths

By default all configuration files contain the default paths used in the Docker container and everything should work without changing any path.
However, it is possible to customize the paths to have your own file organization system.

In the file `easy_gdb/configuration_path.php` you could change the configuration path
 to `/abosolute_path_to/egdb_files/egdb_conf`. By default it is pointing to `/var/www/html/egdb_files/egdb_conf` 
where the files will be placed using the docker container and could be the standard location in a server.

In the file `egdb_files/egdb_conf/easyGDB_conf.php` is possible to set the root path where the `easy_db` folder is.
In the Docker container and usually in a server it could be `/var/www/html`.
Locally, for example, you could have them in `/home/your_user_name/Desktop/example_db`.

Afer the changes, reload the web browser `localhost:8000/easy_gdb/index.php` and check if you can see the home page of easy GDB.

If you want use a different names for your folders remember to change the names in the file paths included in `easy_gdb/configuration_path.php` and `egdb_files/egdb_conf/easyGDB_conf.php`.
For example, for development you could have multiple sites or multiple versions. 
You could easily change between them having different file folders and just changing the path to the active one in `easy_gdb/configuration_path.php`

#### Customize your site

In the configuration file `egdb_files/egdb_conf/easyGDB_conf.php` together with other data files you can customize your site.

Below we will see how to customize each page of the genomic portal step by step.

##### Customize application name and header image

In the configuration file `egdb_files/egdb_conf/easyGDB_conf.php` you can customize the header variables `$dbTitle` and `$header_img` to change the site title and header image.
The images are stored at `egdb_files/egdb_images/`
Try to change them and reload the web browser `localhost:8000/easy_gdb/index.php` to see the changes.

##### Customize logos

In `egdb_files/egdb_images/logos/` you can place logo images, and you can use the file `logos.json` to customize size and link.
Logos are displayed in all pages at the footer.

##### Customize the toolbar

Below, in the toolbar variables, you can customize wich links will be displayed in the toolbar, enabling and disabling the tools and sections available.
A value `1` enable the link and `0` disable it. Choose the links you want to show or hide.

Lets take a look to each one of the links below.

##### Home page

The home page is always available. In the `egdb_custom_text` folder, 
you should be able to see the example text for the welcome page. 
You can open the file `egdb_files/egdb_custom_text/welcome_text.php` in a text editor to customize the content.
It is possible to use PHP or HTML. There you could include CSS and JS. 
Easy GDB uses Bootstrap 4 for the style and some elements.
You could find examples to create you own elements at https://www.w3schools.com/bootstrap4/default.asp

##### About Us

If the variable `$tb_about` is equal `1` in the configuration file (`easyGDB_conf.php`),
then you should be able to see the example text for the `About Us` section.

You can open the file `egdb_files/egdb_custom_text/about.php` in a text editor to customize the content.
Additionally, you can set the about variables `$ab_citation` to `0` or `1` to display, or not, 
the citation of the papers where the genomic database or data were published. 
You can add the citation in `db_citation.php`.

You can also add here information about the participant labs.
For this, the `$ab_labs` should be enabled (equal to `1`) in `egdb_files/egdb_conf/easyGDB_conf.php`.

You can create a json file for each lab or you can copy and modify the provided examples to add your own information.
There, you can include the lab name and, for each person, you can include name, position, a picture (placed in `egdb_files/egdb_images/people/`),
link to a personal page, and, in the more_info array you could add data such as phone, email, and any other custom information.

Every time you change something reload the page `localhost:8000/easy_gdb/about.php` to see the changes.

##### Species

If you want to host data for multiple species or accessions you shold enable the variable `$tb_species = 1`.
If not you can disable it by setting it to `$tb_species = 0`.

Then, you will be able to see them at `localhost:8000/easy_gdb/species.php`

To customize the list of species, use the file `egdb_species/species_list.json`.
There you can include species name, common name, image and link to a 
descriptive custom PHP file (`human.php`, `species1.php` and `species2.php`) 
where you can write all the information about the species.
Create as many PHP species files as you need, customize the content and add them in the `egdb_species/species_list.json` file. 
Images for species menu are placed in `egdb_files/egdb_images/species/`
It is recommendable to use the template as an example to avoid errors.

##### Downloads

You can use the `downloads` folder to can place the files you want to provide for downloading.
The content of that folder will be read and presented in the web site, replicating the folder, subfolder and file structure.
It is recommended to compress the files before place them there for sharing.

You will be able to see this section at `localhost:8000/easy_gdb/downloads.php` or following the link `downloads` in the toolbar.

You can create your own structure of files and folders and place them in the downloads folder.

##### Tools

It is possible to enable or disable the tools `Search page`, `Search box` in toolbar, `BLAST`, `Genome browser`, `Sequence extraction`, `Annotation extraction`, and `Gene version lookup`.
Turn the variables to `1` or to `0` to enable or disable them.

##### Search

To enable the search and the toolbar search box first we must install the PostgreSQL database 
and import the annotations. See https://github.com/noefp/easy_gdb#install-postgresql-1

##### BLAST

In the web browser, follow the link to `BLAST` in the tools toolbar menu. There you will see the BLAST input page.
In `egdb_files/egdb_conf/easyGDB_conf.php`, you can change the input example sequence changing the variable `$blast_example`.

To add BLAST datasets we need to copy the blast databases in the `blast_dbs` folder (path can be changed in `easyGDB_conf.php`).

You can use the next command lines to create your own blast databases from fasta sequence files:

For protein sequences:

    makeblastdb -in your_protein_sequence_file.fasta -dbtype 'prot' -parse_seqids

For nucleotide sequences:

    makeblastdb -in your_nucleotide_sequence_file.fasta -dbtype 'nucl' -parse_seqids

It is important to use the option `-parse_seqids` to create the indexes needed to extract sequences, 
which will be used by the gene views and the `Sequence extraction` tool.

The variable `$max_blast_input` (in `egdb_files/egdb_conf/easyGDB_conf.php`) 
controls the maximum number of sequences allowed as input in `BLAST`.


##### Sequence extraction

This tool uses the datasets in the `blast_dbs` folder to extract the sequences from a list of genes.

If you have the folder `blast_dbs` and added the blast databases there (explained above), then the tool should be ready to use.
You can modify the example input gene list changing the variable `$input_gene_list` in `easyGDB_conf.php`.

The variable `$max_extract_seq_input` (in `easyGDB_conf.php`) controls the maximum number of input gene names to extract.

##### Genome browser

As we ran the setup file after cloning easy GDB, at this point, and example of JBrowse should be ready.
You should be able to check it following the `Tools/Genome Browser` link in the menu bar, or at http://localhost:8000/jbrowse/.


For more information about how to add a new species and to add tracks see `Install and set up JBrowse` below (https://github.com/noefp/easy_gdb#install-and-set-up-jbrowse).

##### Annotation extraction

To enable the annotation extraction first we must install the PostgreSQL database and import the annotations.
See https://github.com/noefp/easy_gdb#install-postgresql-1

##### Gene version lookup:

It should work correctly if some lookup files are placed in the `lookup` folder.
Remove the provided examples and create your own lookup files following the same format.
This tool is useful to, for example, get or provide a list of identifiers of the closest model organism genes, 
different gene versions or orthologs in other species.

The variable `$max_lookup_input` (in `easyGDB_conf.php`) controls the maximum number of gene names allowed as input.

##### More custom pages

Enabling the variable `$tb_more` in `egdb_files/egdb_conf/easyGDB_conf.php` we will see a new tab in the toolbar
called More. There, you could add as many custom pages as you want. They are stored at  `egdb_files/custom_text/custom_pages/`,
and we included two examples called `genome.php` and `other page.php`. You just need to create your own PHP pages and place them there.
The name shown in the toolbar will be taken from the file name, and the content will be automatically wrapped by the header and footer.

Here, for example you can include statistics of your genome assembly, news and events page or anything you like.


####  Customize JBrowse

An example of JBrowse is already implemented but 
when you want to include the genome browser for your species of interest you can find more information in the 
JBrowse manual (http://gmod.org/wiki/JBrowse_Configuration_Guide#prepare-refseqs.pl). Additionally, below you can find some suggestions.

Open a terminal using docker-compose or Docker desktop

    docker-compose exec easy_gdb /bin/bash
    
Upload your sequences to JBrowse. This is how the gene models were uploaded in the example:

    jbrowse$ bin/prepare-refseqs.pl --fasta ../easy_gdb/templates/jbrowse/genome.fasta --out data/easy_gdb_sample
    jbrowse$ bin/flatfile-to-json.pl -gff ../easy_gdb/templates/jbrowse/gene_models.gff --key "EasyGDB gene models" --trackLabel egdb_gene_models --trackType CanvasFeatures --type mRNA --out data/easy_gdb_sample
    jbrowse$ bin/generate-names.pl --tracks egdb_gene_models --out data/easy_gdb_sample/

When adding new tracks, edit the file `data/easy_gdb_sample/trackList.json` to customize them in JBrowse.
Below there is an example of the gene model track with link to the database (`url`).

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

To allow multiple genome browser species, accessions or versions we need to modify the file `data/easy_gdb_sample/tracks.conf`
to include the folder name where the data are stored (remember the jbrowse folder name in import_genes.pl)
    [general]
    dataset_id = easy_gdb_sample



In the file `jbrowse/jbrowse.conf` we can include as many species as we want. It is possible also to include external links in the URL field.
Here we use the easy GDB example and the volvox and yeast examples from JBrowse:
    [datasets.easyGDB]
    url  = ?data=data/easy_gdb_sample
    name = Easy GDB Example

    [datasets.volvox]
    url  = ?data=sample_data/json/volvox
    name = Volvox Example

    [datasets.yeast]
    url  = ?data=sample_data/json/yeast
    name = Yeast Example


#### Private application

In the file easy_gdb_apache.conf we are overwriting the apache configuration insidie the Docker repository.
There there is a block of code that is commented out. If you want to have a private genomics database you can
enalbe that piece of code to make private everything in /var/www/html/.

        <Directory "/var/www/html">
            AuthType Basic
            AuthName "Restricted Content"
            AuthUserFile /etc/apache2/.htpasswd
            Require valid-user
        </Directory>


Create the first user to access private data (Create the passwdfile. If passwdfile already exists, it is rewritten and truncated.)
    htpasswd -c /etc/apache2/.htpasswd First_user

Add new user
    htpasswd /etc/apache2/.htpasswd another_user


#### Start local server

In many cases, after applying some changes you will need to restart the server to make the changes effective.
In a local installation you can stop the application and them start it again from the terminal using the next command:

```bash
php -S localhost:8000
```

Or restarting the the docker-compose service when using the Docker container.

In a server:
```bash
sudo service apache2 restart
```

### Instalation in linux system (without Docker)

#### Install Git, PHP, BLAST and useful tools

Lets install git to download the easy GDB code, PHP to be able to run the web and some other useful tools.
```bash
sudo apt-get update
apt-get install gcc
apt-get install libpq-dev
apt-get install git
apt-get install php
apt-get install zlib1g-dev
apt-get install libexpat1-dev
apt-get install ncbi-blast+
apt-get install vim
apt-get install less
apt-get install wget
apt-get install zip
```

#### Install Perl dependencies (for JBrowse and importing scripts)

    apt-get install cpanminus
    cpanm -L ~/local-lib/ DBI
    cpanm -L ~/local-lib/ Term::ReadKey
    cpanm -L ~/local-lib/ DBD::Pg
    cpanm -L ~/local-lib/ local::lib
    cpanm -L ~/local-lib/ PerlIO::gzip

#### load local-lib in Perl5lib
    vim ~/.bashrc

Add the line below at the end of the file. Remember to change your user name.

    export PERL5LIB=/home/your_username/local-lib/lib/perl5:$PERL5LIB
    source ~/.bashrc


#### Install PostgreSQL 

[Already installed in the Docker container] To install Postgres you can follow the instructions at:
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


#### Install easy GDB

Then, let's create a folder to contain your genomic database. Use the name you like. For this example we will use `example_db`. 
Or you can just go to /var/www/html/ and clone easy GDB there.

    $ mkdir example_db
    $ cd example_db

Then follow the steps as in https://github.com/noefp/easy_gdb#set-up-easy-gdb-using-the-template-example-and-docker, 
but directly in your terminal, not using any of the docker commands.

    example_db$ git clone https://github.com/noefp/easy_gdb.git

    example_db$ cd easy_gdb/install/

    example_db/easy_gdb/install$ bash setup.sh

    example_db/easy_gdb/install$ ls -lh /var/www/html

    example_db/easy_gdb/install$ cd ../../
    
You can run this command in a screen or in the background:
    example_db$ php -S localhost:8000

In web browser (Chrome, Firefox, etc) go to: `localhost:8000/easy_gdb/`

open the file `egdb_files/egdb_conf/database_access.php`.

```php
function getConnectionString(){return "host=localhost dbname=annot1 user=web_usr password=password";};
```

##### Set up password

If we installed PostgreSQL from scratch we need to create a password for postgres (it would be like the database default/root user).

You can use `\q` to exit the PostgreSQL console.

Connect to the database the first time:
```bash
sudo -u postgres psql postgres
```

Create a password for the postgres user:
```sql
\du
\password postgres
You will be asked to type your new password
\q
```



    example_db$ psql -U postgres

In Postgres console

    CREATE DATABASE annot1;

    CREATE ROLE web_usr WITH LOGIN ENCRYPTED PASSWORD 'tmp_password' CREATEDB;
    \password web_usr

    \q

Back in your bash terminal:

    example_db$ psql -U postgres -d annot1 -h localhost -a -f easy_gdb/scripts/create_annot_schema2.sql

    example_db$ perl easy_gdb/scripts/import_genes.pl egdb_files/annotations/gene_list.txt "Homo sapiens" "1.0" "easy_gdb_sample"
    
    example_db$ perl easy_gdb/scripts/import_annots_sch2.pl egdb_files/annotations/annotation_example_SwissProt.txt SwissProt "Homo sapiens" "1.0"
    
    example_db$ perl easy_gdb/scripts/import_annots_sch2.pl egdb_files/annotations/annotation_example_TAIR10.txt TAIR10 "Homo sapiens" "1.0"


#### Set up server

In a server (not mandatory for local instalations) you would need to use Apache or Nginx webservers to host your application in a server.
For example, you could install apache:

```bash
sudo apt-get install apache2

cd /etc/apache2/
sudo cp 000-default.conf easy_gdb.conf
sudo a2dissite 000-default.conf
sudo a2ensite easy_gdb.conf
systemctl reload apache2
```

In many cases, after applying some changes you will need to restart the server to make the changes effective:
```bash
sudo service apache2 restart
```





