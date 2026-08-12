<!-- # EasyGDB -->
![easy_gdb_logo.png](/_resources/easy_gdb_logo.png)

Welcome to ***EasyGDB***. It is a system designed to simplify the implementation of genomics portals and minimize their maintenance. EasyGDB genomics portals can include tools such as Expression atlas, BLAST, Genome browser (JBrowse), genetic variation explorer, file downloads, sequence extraction, annotation search, bulk annotation extraction, gene list lookup, and germplasm passport and phenotype tools.

>Noe Fernandez-Pozo, Aureliano Bombarely, EasyGDB: a low-maintenance and highly customizable system to develop genomics portals, Bioinformatics, Volume 38, Issue 16, August 2022, Pages 4048–4050,
<a href="https://doi.org/10.1093/bioinformatics/btac412" target="blank" class="link hover">https://doi.org/10.1093/bioinformatics/btac412
</a>

For more information check [this YouTube playlist](https://youtu.be/JTE-8zR5ogk).


Some examples of sites developed using EasyGDB are:

-   [MangoBase](https://mangobase.org/)
-   [OliveAtlas](https://www.oliveatlas.uma.es/)
-   [IHSM Subtropicals DB](https://ihsmsubtropicals.uma.es/)
-   [SkeletalAtlas](https://www.skeletalatlas.uma.es/)
-   [AvoBase](https://www.avocado.uma.es/)
-   [MAdLandExpr](https://peatmoss.plantcode.cup.uni-freiburg.de/easy_gdb/index.php)
-   [Ae. arabicum DB](https://plantcode.cup.uni-freiburg.de/aetar_db/index.php)
<br><br>

# Table of contents
-   [Requirements](#requirements)

- [Installation]()
  -   [Installation with Docker](#installation-with-docker)
  -   [Installation without Docker (only for Linux Systems)](#installation-without-docker-only-for-linux-systems)
      - [Install Git, PHP, BLAST and useful tools](#install-git-php-blast-and-useful-tools)
      - [Install Perl dependencies for JBrowse and importing scripts]()
      - [Load local-lib in Perl5lib](#load-local-lib-in-perl5lib)
      - [Install EasyGDB](#install-easygdb)
-   [Customization](#customization)
    -   [Customize file paths](#customize-file-paths)
    -   [Customize your site](#customize-your-site)
        - [Toolbar](#toolbar)
        - [Home page](#home-page)
        - [More custom pages](#more-custom-pages)
    -   [Other resources](#other-resources)
        - [About us](#about-us)
        - [Species](#species)
        - [Downloads](#downloads)
- [Tools](#tools)
  - [Annotations File Database](#annotation-file-database)
  - [Basic tools](#basic-tools)
    - [Search](#search)
    - [Annotation extraction](#annotation-extraction)
    - [Genome browser](#genome-browser)
    - [BLAST](#blast)
    - [Gene version lookup](#gene-version-lookup)
    - [Sequence explorer](#sequence-explorer)
    - [Sequence extraction](#sequence-extraction)
    - [Gene Set Enrichment](#gene-set-enrichment)

  -  [Gene expression atlas tools](#gene-expression-atlas-tools)
      - [Configuration](#configuration)
      - [Expression viewer](#expression-viewer)
        - [Configuration](#required-files-and-folder)
        - [Customization](#customise-the-expression-color-scale)
        <!-- - [Expression viewer](#expression-comparator) -->
      - [Expression comparator](#expression-comparator)
      - [Coefficient of variation calculator](#coefficient-of-variation-calculator)
      - [Co-expression search](#co-expression-search)
  -  [Passport and phenotype tools](#passport-and-phenotype-tools)
        - [Configuration](#easyGDB_conf)
        - [Gallery](#Gallery)
        - [Customization](#custom)
-   [JBrowse](#jbrowse)
  -  [Private application](#private-application)
  - [Set up EasyGDB PostgreSQL database (optional, not recommended)](#set-up-easygdb-postgresql-database-optional-not-recommended)
    - [Install PostgreSQL](#install-postgresql)
    -   [Set up EasyGDB PostgreSQL database in Docker](#set-up-easygdb-postgresql-database-in-docker)
        -   [Set up password in PostgreSQL](#set-up-password-in-postgresql)
        -   [Create a new database](#create-a-new-database)
        -   [Create a new role (DB user)](#create-a-new-role-db-user)
        -   [Import annotations](#import-annotations)
    -   [Set up EasyGDB PostgreSQL database in Linux or servers](#set-up-easygdb-postgresql-database-in-linux-or-servers)
        -   [Set up password](#set-up-password)
        -   [Set up server](#set-up-server)

# Requirements

EasyGDB can be implemented in Linux, MacOS and Windows. You can use [Docker](<https://docs.docker.com/get-docker/>) to install our EasyGDB container or follow the steps at the bottom to install EasyGDB from scratch in a Linux System ([Installation without Docker (only for Linux Systems)](#installation-without-docker-only-for-linux-systems)).

# Installation

## Installation with Docker

Using Docker, the first step will be installing [Docker Desktop](<https://docs.docker.com/get-docker/>), if you do not have it already.

To run docker commands you will need to start the Docker Desktop app, or run the docker engine/daemon in the background.

Now we will use git to clone the EasyGDB docker-compose repository: Open a terminal, go to the path where you want to save your genomics portal code, and clone the easyGDB_docker repository from GitHub using the next command:
```
git clone https://github.com/noefp/easyGDB_docker.git
```
Go into the easyGDB_docker folder

```
cd easyGDB_docker/src
```
Clone the easy_gdb repository

```
git clone https://github.com/noefp/easy_gdb.git
```
Then, build the container (inside the easyGDB_docker folder; Docker Desktop should be started):

``` 
docker compose build
```

Start the container using the Docker desktop application, or use the terminal, in the easyGDB_docker folder, to run:

```
docker compose up -d
```

The flag -d (--detach) allows to detach the docker in the terminal. It is necessary to start the container (it can be done using the terminal or the Desktop application) everytime you want to access the genomic portal, using `docker compose start`. You can stop it with `docker compose stop`. `docker_compose ps` will let you know which containers are running (The Docker Desktop application allows to start and stop the easyGDB container using a user-friendly graphical interface).


You should be able to see the folders `annotations`, `apache`, `blast_dbs`, `db_example_annotations`, `downloads`, `easy_gdb`, `egdb_files`, `expression_data`, `jbrowse`, `jbrowse_example_data`, `lookup`, `passport`, `private_expression_data` and `vcf`. Inside these folders there are some example templates to help you customize your own genomics web portal.

At this moment all the features of easy_gdb should be already available. In a web browser (Chrome, Firefox, etc.) go to: `localhost:8000/easy_gdb/`. You should be able to see an example of EasyGDB running.

![easy GDB home](/_resources/easy_gdb_home.png)


## Installation without Docker (only for Linux Systems)

It should be easy to install it in a linux computer (everything was tested on Ubuntu), such as the ones usually provided in servers to host genomics portals. In most of the servers it is probable that some of the tools needed are already installed, and if you work often with linux you would probably have some of them already.

### Install Git, PHP, BLAST and useful tools

Lets install git to download the easy GDB code, PHP to be able to run the web and some other useful tools.

``` bash
sudo apt-get update
sudo apt-get install gcc
sudo apt-get install libpq-dev
sudo apt-get install git
sudo apt-get install php
sudo apt-get install zlib1g-dev
sudo apt-get install libexpat1-dev
sudo apt-get install ncbi-blast+
sudo apt-get install vim
sudo apt-get install less
sudo apt-get install wget
sudo apt-get install zip
sudo apt-get install make
sudo apt-get install lsb-release
sudo apt-get install tabix
```

### Install Perl dependencies for JBrowse and importing scripts

``` bash
sudo apt-get install cpanminus
cpanm -L ~/local-lib/ DBI
cpanm -L ~/local-lib/ Term::ReadKey
cpanm -L ~/local-lib/ DBD::Pg
cpanm -L ~/local-lib/ local::lib
cpanm -L ~/local-lib/ PerlIO::gzip
```

### Load local-lib in Perl5lib

```
vim ~/.bashrc
```
Add the line below at the end of the file. Remember to change your username.

```
export PERL5LIB=/home/your_username/local-lib/lib/perl5:$PERL5LIB
```
Enable the changes in the opened terminal. source \~/.bashrc


### Install EasyGDB

Ideally in a server or Linux system you should clone EasyGDB at /var/www/html

Alternatively, if you do not have permissions or prefer otherwise, you could create a folder to contain your genomic database, using the location and name you like. For example you could use `example_db`, and you could create the folder with next command:

``` 
mkdir example_db
```

Enter into the folder (cd /var/www/html recommended in Linux servers):

```
cd example_db
```
or

``` 
cd /var/www/html
```
Then, at /var/www/html or example_db, clone the repository:

``` 
git clone https://github.com/noefp/easy_gdb.git
```
Go to the install folder:

```
cd easy_gdb/install/
```
Run the setup script (inside the install folder):

```
sudo bash setup.sh
```
Go back from the install folder to the example_db/ or /var/www/html/ folder:

```
cd ../../
```
To start the PHP server that run the service to show the web, you can run this command in the same location where you installed easy_gdb:

```
php -S localhost:8000
```

Now, the genomics portal should be available at the web browser (Chrome, Firefox, etc), in the URL: `localhost:8000/easy_gdb/`

Do not forget to change the configuration path in the file `configuration_path.php`. By default it is `/var/www/html`, for the Docker installation and for linux servers. If you used a different path (for example_db), you should change the variable from `$conf_path = "/var/www/html/egdb_files/egdb_conf"` to your path, for example `$conf_path = "/home/user/example_db/egdb_files/egdb_conf"`.

Then open the file `easyGDB_conf.php` in the folder `egdb_files/egdb_conf/` and change the `$root_path` to the path where you installed the example_db, in the previous example `$root_path = "/home/user/example_db"`.

<br>


# Customization

## Customize file paths

The configuration file `easyGDB_conf.php`, contains the default paths for every feature and everything should work without changing any path. However, it is possible to customize the paths to have your own file organization system.

The `configuration_path.php` file contains the path to the current project folder (`/var/www/html/egdb_files/egdb_conf` by default). The `egdb_files/egdb_conf/easyGDB_conf.php` file contains the project configuration.

In the file `configuration_path.php` you could change the configuration path to `/abosolute_path_to/egdb_files/egdb_conf`. By default it is pointing to `/var/www/html/egdb_files/egdb_conf` where the files will be placed using the docker container and the standard location in a server.

In the file `egdb_files/egdb_conf/easyGDB_conf.php` it is possible to set the root path where the `easy_db` folder is. In the Docker container and usually in a server it would be `/var/www/html`. Locally, for example, you could have them in `/home/your_user_name/Desktop/example_db`.

If you want to use different names for your project folders, remember to change the names in the file paths included in `configuration_path.php` and `egdb_files/egdb_conf/easyGDB_conf.php`. 

For example, for development you could copy the `egdb_files` and rename it to the name of your project, repeating this process as many times as projects you have. That way you could manage multiple sites or multiple versions. You could easily change between them just commenting out all the path lines but the active one in `configuration_path.php`.

>[!TIP]
 One way to manage multiple sites is to create a copy of the `egdb_files` folder with its content and add it to the `$conf_path` in the configuration file. Then, write the new name in the value of the `$egdb_files_folder` variable at `egdb_conf/easyGDB_conf.php`. It is a good practice to always create a copy of the `egdb_files` folder to always keep a reference of the example configuration while you can replace the example data with your own data in the active project folder.*

Example of the `configuration_path.php` file:

``` php
<?php
    //$conf_path = "/var/www/html/egdb_files/egdb_conf";
    //$conf_path = "/var/www/html/project1/egdb_conf";
    //$conf_path = "/var/www/html/project2/egdb_conf";
    $conf_path = "/var/www/html/active_project/egdb_conf";
?>
```

After the changes, reload the web browser `localhost:8000/easy_gdb/index.php` and check if you can see the home page of EasyGDB. Sometimes it is important to empty the web browser cache to be able to visualize the changes, especially when new images are added.

## Customize your site

In the configuration file `egdb_files/egdb_conf/easyGDB_conf.php` together with other JSON files at `egdb_files/json_files`, you can customize your site.

Below we will see how to customize each page of the genomic portal step by step.

### Application name and header image

In the configuration file `egdb_files/egdb_conf/easyGDB_conf.php` you can customize the header variables `$dbTitle`, `$header_img` and `$db_logo` to change the site title, header image and site logo. The images are stored at `egdb_files/egdb_images/`. Try to change them and reload the web browser `localhost:8000/easy_gdb/index.php` to see the changes.

```PHP
$dbTitle=  "Your site title"
$header_img = "your header image file name";
$db_logo=  "your toolbar logo file name";
```

If `header_img=""` then remove the header and put the toolbar at the top.

### Footer logos

In `egdb_files/egdb_images/logos/` you can place logo images (institutions, funding agencies, etc.), and you can use the file `egdb_files/json_files/customization/logos.json` to customize size and link. Logos are displayed in all pages at the footer.

### Toolbar

Below, in the toolbar variables, you can customize the links that will be displayed in the toolbar, enabling and disabling the tools and sections available. A value `1` enables the link and `0` disables it. Choose the links you want to show or hide.

Additionally, you can enable the variable `$tb_custom` to add your own links to the toolbar just by editing the `custom_toolbar.php` file in the `egdb_custom_text` directory. If you need to customize your home link (the site name by default defined by the variable `$dbTitle`), you can set `$tb_rm_home` to 1, to remove the site title, so you can create your own in `custom_toolbar.php`.

Let's take a look at each one of the links below.

### Home page

The home page is always available. In the `egdb_custom_text` folder, you should be able to see the example text for the welcome page. You can open the file `egdb_files/egdb_custom_text/welcome_text.php` in a text editor to customize the content. It is possible to write code in PHP or just HTML (do not change the extension of the file). There, you could include CSS and JS. EasyGDB uses Bootstrap 4 for the style and some elements. You could find examples to create your own elements at <https://www.w3schools.com/bootstrap4/default.asp>

### More custom pages

Enabling the variable `$tb_more` in `egdb_files/egdb_conf/easyGDB_conf.php` we will see a new tab in the toolbar called More. There, you could add as many custom pages as you want. They are stored at `egdb_files/custom_text/custom_pages/`, and we included two examples called `genome.php` and `other page.php`. You just need to create your own PHP pages and place them there. The name shown in the toolbar will be taken from the file name, and the content will be automatically wrapped by the header and footer.

Here, for example you can include statistics of your genome assembly, news and events page, links of interest to other sites, or anything you like.

#### Tables

In the `custom_pages` directory we can find an example of a custom page to visualize tab delimited files as formatted tables.

The file `table_menu.php` provides the code to list all the files in the folder `custom_pages/tables`.

	http://localhost:8000/easy_gdb/custom_view.php?file_name=table_menu.php

The file `table_to_page.php` is a template to format the file passed in the URL using the variable `table_name`. 

    http://localhost:8000/easy_gdb/custom_view.php?file_name=table_to_page.php&table_name=tables/table_example.txt&link_field=ACC%20Name

The variable `link_field` passed in the URL defines which column contains an unique id that can be used to link to the page `row_data.php`, which is a template to show the data contained in the row of the table where the linked id belongs to. In the example, as ´ACC Name (contains a space in the URL that is written as `ACC%20Name`).

## Other Resources
### About Us

If the variable `$tb_about` is equal to `1` in the configuration file (`easyGDB_conf.php`), then you should be able to see the example text for the `About Us` section.

You can open the file `egdb_files/egdb_custom_text/about.php` in a text editor to customize the content. Additionally, you can set the about variables `$ab_citation` to `0` or `1` to display, or not, the citation of the papers where the genomics portal or data were published. You can add the citation in `db_citation.php`.

You can also add here information about the participant labs. For this, the `$ab_labs` should be enabled (equal to `1`) in `egdb_files/egdb_conf/easyGDB_conf.php`.

You can customize the lab's JSON file (`egdb_files/json_files/customization/labs.json`) to add your own information. There, you can include the lab name and, for each person, you can include name, position, a picture (placed in `egdb_files/egdb_images/people/`), link to a personal page, and, in the more_info array you could add data such as phone, email, and any other custom information.

Every time you change and save the `about.php` file, reload the page `localhost:8000/easy_gdb/about.php` to see the modifications.

### Species

If you want to host data for multiple species or accessions you should enable the variable `$tb_species = 1`. If not you can disable it by setting it to `$tb_species = 0`.

Then, you will be able to see them at `localhost:8000/easy_gdb/species.php`

To customize the list of species, use the file `egdb_files/json_files/customization/species_list.json`. There, you can include species name, common name, image and link to a descriptive custom PHP file (`human.php`, `species1.php` and `species2.php`) where you can write all the information about the species. Create as many PHP species files as you need, customize the content and add them in the `egdb_files/json_files/customization/species_list.json` file. Images for species menu are placed in `egdb_files/egdb_images/species/` It is recommendable to use the template as an example to avoid errors.

### Downloads

You can use the `downloads` folder to place the files you want to provide for downloading. The content of that folder will be read and presented in the web site, replicating the folder, subfolder and file structure. It is recommended to compress the files before placing them there for sharing.

You will be able to see this section at `localhost:8000/easy_gdb/downloads.php` or by following the link `downloads` in the toolbar.

You can create your own structure of files and folders and place them in the downloads folder.

It is also compatible with H5AI (https://github.com/lrsjng/h5ai), which could be linked to `/downloads/` 

by placing the folder _h5ai inside `downloads`

and adding the next line to the enabled site in Apache:

```
 DirectoryIndex  index.html  index.php  /_h5ai/public/index.php
```

# Tools

## Annotation File Database

Some tools such as *Search*, *Annotation Extraction*, the *gene annotation pages* and the *Gene Expression Atlas*, will require access to the gene annotations.

To set up the functional annotation file database you just need to place a tab-delimited file (.txt) with the annotations of the genes in the `annotations` directory in the `$annotations_path`, which by default in the configuration file ([`easyGDB_conf.php`](https://github.com/noefp/easyGDB_docker/blob/main/src/egdb_files/egdb_conf/easyGDB_conf.php)) is `$root_path/annotations` equivalent to src (`/var/www/html/annotations`).

You can place multiple annotation files or multiple subfolders with their respective annotation files inside.

The annotation file should include the gene names in the first column and the annotation IDs and descriptions in the next columns.

The header should include the name of the databases (Araport11, SwissProt, InterPro) as it is written in the `$json_files_path/tools/annotation_links.json` file, where the links to these databases are included. That way the database identifiers could be automatically linked to their databases.



| Gene | Araport11 | Araport11 Description | SwissProt | SwissProt Description | InterPro | InterPro Description |
| --- | --- | --- | --- | --- | --- | --- |
| gene1.1 | AT3G19210.3 | DNA repair/recombination protein | Q0PCS3 | Protein CHROMATIN REMODELING 25 (AtCHR25) | IPR014001;IPR000330;IPR001650 | Helicase superfamily;SNF2, N-terminal;Helicase, C-terminal |



It is important to include the word "Description" in the columns of the header including functional description.

The annotations including more than one ID should be separated by ";", as shown in the InterPro example.

You can add custom annotation links in the annotation_links.json file: `egdb_files/json_files/tools/annotation_links.json`

```json
{
  "TAIR10":"http://www.arabidopsis.org/servlets/TairObject?type=locus&name=query_id",
  "Araport11":"http://www.arabidopsis.org/servlets/TairObject?type=locus&name=query_id",
  "SwissProt":"http://www.uniprot.org/uniprot/query_id",
  "InterPro":"https://www.ebi.ac.uk/interpro/entry/InterPro/query_id",
  "NCBI":"https://www.ncbi.nlm.nih.gov/protein/query_id"
}
```
The value `query_id` will be automatically replaced by the database identifier to form the correct link.

## Basic tools

<!-- It is possible to enable or disable the dropdown menu `Tools` by switching the variable `$tb_tools` to 1 or 0 in the `easyGDB_conf.php` file. Additionally, each tool can be enabled or disabled in the tools menu: `$tb_search` (Search), `$tb_blast` (BLAST), `$tb_jbrowse` (Genome browser), `$tb_jbrowse2`(Genome browse 2), `$tb_seq_ext` (Sequence extraction), `$tb_annot_ext` (Annotation extraction), `$tb_lookup` (Gene version lookup), `$tb_enrichment` (Gene set enrichment). Turn the variables to `1` or to `0` to enable or disable them. -->
Each *Basic tool* can be included or hidden in the toolbar.<br>
Set each variable to `1` or `0` to enable or disable the corresponding tool in `easyGDB_conf.php` (`egdb_files/egdb_conf/easyGDB_conf.php`). :
- `$tb_tools` - Enable ***Tool***  dropdown in the toolbar .
  - `$tb_search`- Includes the [***Search***](#search) tool link in the toolbar .
  - `$tb_annot_ext` - Includes the [***Annotation extraction***](#annotation-extraction) tool link in the toolbar.
  - `$tb_jbrowse` - Includes the [***Genome browser***](#genome-browser) tool link in the toolbar.
  - `$tb_jbrowse2` - Includes the ***Genome browse 2*** tool link in the toolbar.
  - `$tb_blast` - Includes the [***BLAST***](#blast) tool link in the toolbar.
  - `$tb_seq_exp` -Includes the [***Sequence explorer***](#sequence-explorer) tool link in the toolbar.
  - `$tb_seq_ext` - Includes the [***Sequence extraction***](#sequence-extraction) tool link in the toolbar.
  - `$tb_lookup` - Includes the [***Gene version lookup***](#gene-version-lookup) tool link in the toolbar.
  - `$tb_enrichment` -Includes the [***Gene set enrichment***](#gene-set-enrichment) tool link in the toolbar.



`$tb_search_box` enables the ***Search box*** in the toolbar. It is only available when the relational database is enabled (to be implemented soon for file annotations).


## Search

The Search tool will search all available annotations to find genes and their annotations based on functional description keywords or gene identifiers. To enable the search tool first we must set up the [annotation file database](#annotation-file-database). Then it will work automatically.

## Annotation extraction

This tool allows us to visualize and download all available annotations for a list of genes. To enable the annotation extraction first we must set up the [annotation file database](#annotation-file-database). Then it will work automatically.

## Genome browser

As we ran the setup file after cloning easy GDB, at this point, an example of JBrowse should be ready (If you chose to install JBrowse). You should be able to check it following the `Tools/Genome Browser` link in the menu bar, or at <http://localhost:8000/jbrowse/>.

For more information about how to add a new species and to add tracks see `Install and set up JBrowse` [below](#jbrowse).

## BLAST

In the web browser, follow the link to `BLAST` in the tools toolbar menu. There you will see the BLAST input page (http://localhost:8000/easy_gdb/tools/blast/blast_input.php). In `egdb_files/egdb_conf/easyGDB_conf.php`, you can change the input example sequence by changing the variable `$blast_example`.

To add BLAST datasets we need to copy the blast databases in the `blast_dbs` folder (path can be changed in `easyGDB_conf.php`). The BLAST databases should be copied inside a category folder, such as `category_1` and `category_2` in the example template. If all the BLAST databases are included in a single category folder, there will be only a dropdown menu to select the BLAST database in the select Dataset section in the BLAST input page. If the BLAST databases are organized in two or more category folders, there will be an extra dropdown menu to select the category and then it will be possible to select the databases within the category.

```txt
    blast_dbs/
    |---- category_1/
          |---- proteins.fasta.psq
          |---- proteins.fasta.psi
          |---- proteins.fasta.psd
          |---- proteins.fasta.pog
          |---- proteins.fasta.pin
          |---- proteins.fasta.phr
          |---- nucleotides.fasta.nsq
          |---- nucleotides.fasta.nsi
          |---- nucleotides.fasta.nsd
          |---- nucleotides.fasta.nog
          |---- nucleotides.fasta.nin
          |---- nucleotides.fasta.nhr
    |---- category_2/
          |---- proteins.fasta.psq
          |---- proteins.fasta.psi
          |---- proteins.fasta.psd
          |---- proteins.fasta.pog
          |---- proteins.fasta.pin
          |---- proteins.fasta.phr
```

BLAST database files and category folders MUST NOT include spaces in their names. Underscores can be used in the file and folder names and they will be displayed as spaces in the web. It is also important to avoid special characters.

The template example includes the folders `category_1` and `category_2`, but any other name can be used to name the category folders (without spaces or special characters). For example, we could create folders to organize BLAST databases for several species such as `Danio_rerio`, `Mola_mola`, `Salmo_salar`, etc.

### Create your own BLAST databases

You can use the next command lines to create your own blast databases from fasta sequence files:

For protein sequences:

    makeblastdb -in your_protein_sequence_file.fasta -dbtype 'prot' -parse_seqids

For nucleotide sequences:

    makeblastdb -in your_nucleotide_sequence_file.fasta -dbtype 'nucl' -parse_seqids

It is important to use the option `-parse_seqids` to create the indexes needed to extract sequences, which will be used by the gene views and the `Sequence extraction` tool.

The variable `$max_blast_input` (in `egdb_files/egdb_conf/easyGDB_conf.php`) controls the maximum number of sequences allowed as input in `BLAST`.

You can also add custom links for the BLAST output by customizing the `egdb_files/json_files/tools/blast_links.json` file:

``` json
{
  "sample_blast_DB_genome.fasta":"/jbrowse/?data=data%2Feasy_gdb_sample&loc={chr}%3A{start}..{end}", "sample_blast_DB_proteins.fasta":"/easy_gdb/gene.php?name={subject}&annot=example/gene_annotations.txt",
  "sample_blast_DB_nucleotides.fasta":"#",
  "sample_uniprot.fasta":"https://www.uniprot.org/uniprot/{subject}"
}
```

Just include the name of your database on the left (`sample_blast_DB_proteins.fasta`) and the link on the right (`/easy_gdb/gene.php?name={subject}&annot=example/gene_annotations.txt`). 

You can provide any other links and use the variables in the example (`{subject}`, `{chr}`, `{start}`, `{end}`) to add gene names or coordinates extracted from the BLAST results. In this file several link examples are shown:

-   Link to genome browser: the variables `{chr}`, `{start}`, `{end}` will be replaced by the ones found in the results. Very useful for genome sequence BLAST databases.
-   No link: using a value of `#` will produce self links. For example for genes not included in your easy GDB database.
-   Gene name external links: the variable `{subject}` will be replaced by the subject gene name. It can be used to link to external databases, such as UniProt in the example above.

In the case of the installation of EasyGDB using the PostgreSQL relational database (no longer maintained), the BLAST output will link to the easy GDB gene page by default unless other information is included in the JSON file.

### BLAST with multiple databases

You can blast your sequences against multiple databases in the same query. 
To enable this option, you need to set `$multiple_blast_db` in `egdb_files/egdb_conf/easyGDB_conf.php` to 1. 
Then, you just need to copy the BLAST databases inside the folders `nucleotides` and `proteins` in the `blast_dbs` folder using a different subfolder for each species,
 such as `nucleotides/Danio_rerio/` and `nucleotides/Mola_mola/`. BLAST species folders MUST NOT include spaces in their names.

```txt
    blast_dbs/
    |---- nucleotides/
       |---- species1/
          |---- sps1_mrna.fasta.nsq
          |---- sps1_mrna.fasta.nsi
          |---- sps1_mrna.fasta.nsd
          |---- sps1_mrna.fasta.nog
          |---- sps1_mrna.fasta.nin
          |---- sps1_mrna.fasta.nhr
          |---- sps1_genome.fasta.nsq
          |---- sps1_genome.fasta.nsi
          |---- sps1_genome.fasta.nsd
          |---- sps1_genome.fasta.nog
          |---- sps1_genome.fasta.nin
          |---- sps1_genome.fasta.nhr
       |---- species2/
          |---- sps2_mrna.fasta.nsq
          |---- sps2_mrna.fasta.nsi
          |---- sps2_mrna.fasta.nsd
          |---- sps2_mrna.fasta.nog
          |---- sps2_mrna.fasta.nin
          |---- sps2_mrna.fasta.nhr
    |---- proteins/
       |---- species1/
          |---- sps1_proteins.fasta.psq
          |---- sps1_proteins.fasta.psi
          |---- sps1_proteins.fasta.psd
          |---- sps1_proteins.fasta.pog
          |---- sps1_proteins.fasta.pin
          |---- sps1_proteins.fasta.phr
       |---- species2/
          |---- sp2_proteins.fasta.psq
          |---- sp2_proteins.fasta.psi
          |---- sp2_proteins.fasta.psd
          |---- sp2_proteins.fasta.pog
          |---- sp2_proteins.fasta.pin
          |---- sp2_proteins.fasta.phr
```

## Sequence Explorer

The Sequence Explorer tool enables users to retrieve and visualise genomic, transcript and protein sequences for any gene in the database. It provides coloured sequence views that highlight exons, introns, UTRs and flanking regions. The tool also integrates with JBrowse for genome visualisation and with BLAST for sequence similarity searches.



### Configuration file

The tool is configured through the file `seq_exp.json` located at `egdb_files/json_files/tools/seq_exp.json`. Each entry in the file defines one organism and its associated data files.

```json
{
  "Example_species": {
    "gene_names_file": "gff_tool/gene_names/example_gene_names.txt",
    "gff_file": "gff_tool/gff_files/example_genome.gff3.gz",
    "jbrowse": "http://localhost:8000/jbrowse/?data=data%2Feasy_gdb_sample&loc=Chr1%3A627..659&tracks=DNA%2Cegdb_gene_models&highlight=00",
    "blast_db": "blast_dbs/Example_species/example_genome.fasta"
  }
}
```

The `seq_exp.json` file supports the following parameters per organism:

- **`gene_names_file`** — path to a plain text file with one gene ID per line. This file is used to power the autocomplete search box.
- **`gff_file`** — path to the genome annotation file in GFF/GFF3 format, compressed with gzip or bgzip.
- **`blast_db`** — path to the BLAST nucleotide database for the genome (without file extension).
- **`jbrowse`** — *(optional)* URL template for the JBrowse genome browser. If this field is absent or empty, the JBrowse panel will not be shown.

### Required data files

For each organism, the following files must be present:

| File | Description |
|------|-------------|
| `gene_names.txt` | One gene ID per line, used for autocomplete |
| `annotation.gff3.gz` | Gene annotation in GFF/GFF3 format, gzip compressed |
| `genome.fasta.n*` | BLAST nucleotide database files (`.nhr`, `.nin`, `.nsq`, etc.) |

### Extract gene IDs from the GFF/GFF3 files:

Users must create the file 'gene_name.txt'. They can do this using the following commands :

```bash
zcat annotation.gff3.gz | awk '$3=="gene"' | grep -oP 'ID=\K[^;]+' > gene_names.txt
```

If the GFF uses the `Name` attribute instead of `ID` for gene names:

```bash
zcat annotation.gff3.gz | awk '$3=="gene"' | grep -oP 'Name=\K[^;]+' > gene_names.txt
```
This is an example of how the file should be formatted:

```txt
Gene_1
Gene_2
Gene_3
Gene_4
Gene_5
Gene_6
Gene_7
Gene_8
Gene_9
Gene_10
```


## Sequence extraction

The Sequence Extraction tool allows downloading the sequences (proteins, transcripts, CDS) for a list of genes. This tool uses the datasets in the `blast_dbs` folder to extract the sequences.

If you have the folder `blast_dbs` and added the blast databases there (explained above), then the tool should be ready to use. You can modify the example input gene list by changing the variable `$input_gene_list` in `easyGDB_conf.php`.

The variable `$max_extract_seq_input` (in `easyGDB_conf.php`) controls the maximum number of input gene names to extract.


## Gene version lookup

The Gene version lookup tool is useful to convert a list of gene identifiers to the equivalent list of genes in a different annotation version, or equivalent orthologs in other species, such as the closest model organism. This tool should work if some lookup files are placed in the `lookup` folder. Remove the provided examples and create your own lookup files following the same format. 
>[!Note]
 In the first row, add a header that indicates what the gene identifier represents.

```txt
Gene_V1    Gene_V2
gene1.1	gene1_v2.1
gene2.1	gene2_v2.1;gene3_v2
gene3.1	gene4_v2.1
gene_c1_10000V3.1	gene_s139_2V2.1
gene_c1_1000V3.1	gene_s200_100V2.1;gene_s200_101V2.1
gene_c1_10010V3.1	gene_s139_3V2.1
gene_c1_10040V3.1	gene_s139_5V2.1
gene_c1_10050V3.1	gene_s139_6V2.1
```

The variable `$max_lookup_input` (in `easyGDB_conf.php`) controls the maximum number of gene names allowed as input.


## Gene Set Enrichment

This tool allows to perform a gene set enrichment analysis through the tool g:Profiler. It requires a lookup file to convert the query gene IDs to one of the available species at *g:Profiler* (https://biit.cs.ut.ee/gprofiler/gost). If your species is already available then it is possible to submit the list of query genes directly. To enable this tool it is necessary to fill out the `egdb_files/json_files/tools/enrichment.json` file.

In the example below, the title "A.thaliana" is the name shown for selection in the input page. "gprofiler_sps" : "athaliana" is the name required in the g:Profiler tool. Available species IDs can be found at https://biit.cs.ut.ee/gprofiler/page/organism-list.

``` json
{
  "A.thaliana":
    {"gprofiler_sps" : "athaliana",
      "lookup_files":
      [
        "Species1_Arabidopsis_Best_hit.txt",
        "Species2_Arabidopsis_Best_hit.txt"
      ]
    }
}
```
<br>

# Gene expression atlas tools
This module provides management of expression data and integrates tools.



## Configuration
<!-- To enable this tool, it is necessary to activate the next options in `easyGDB_conf.php` : -->
Each *Expression atlas tool* can be included or hidden in the toolbar.<br>
Set each variable to `1` or `0` to enable or disable the corresponding tool in `easyGDB_conf.php` (`egdb_files/egdb_conf/easyGDB_conf.php`) :
- `tb_gene_expr` - Enable the ***Expression atlas***  dropdown in the toolbar.
  - `tb_expr_viewer` - Includes [***Expression viewer***](#expression-viewer) tool link in the toolbar.
  - `tb_expr_comparator` - Includes the [***Expression comparator***](#expression-comparator) tool link in the toolbar.
  - `tb_cv_calculator` - Includes the [***Coefficient of Variation calculator***](#coefficient-of-variation-calculator) tool link in the toobar.
  - `tb_expr_datasets` - Includes the ***Datasets Information page*** link in the toolbar (shows all PHP files stored in the `egdb_custom_text/expr_datasets/` directory).
  - `tb_coexpr` - Includes [***Co-expression***](#co-expression-search) link in the toolbar.

- To enable the tool’s features, you must set the following variables:
  - `expr_menu` - Enable a ***Dataset Information*** link in the expressions tools (*viewer and comparator*) that displays all dataset descriptions. ([description](#expression_infojson))
  - `comparator_lookup` - Enable the ***gene version lookup*** checkbox in the *Expression comparator* tool.

> [!NOTE]
> If  `tb_gene_expr` = 1 and the individual activation variables (`tb_expr_viewer`, `tb_expr_comparator`, `tb_cv_calculator`, `tb_expr_datasets`) are **not defined** (the variables do not exist), the expression tool (*viewer, comparator, CV calculator, and datasets information page*) are enabled by default.
To disable any specific expression tool, set its corresponding variable to 0 instead of removing it.

## Expression viewer
## Configuration
### Required files and folder
The expression datasets should be placed in the `expression_data` folder (by default defined as `$expression_path` = `$root_path/expression_data` in [`easyGDB_conf.php`](https://github.com/noefp/easyGDB_docker/blob/main/src/egdb_files/egdb_conf/easyGDB_conf.php)). 

Additionally, the `egdb_files/json_files/tools/expression_info.json` file will allow you to customize the gene expression atlas.

Inside the `expression_data` directory it is possible to organize the expression datasets in subfolders to separate them by technology (RNA-seq, microarray, proteomics, metabolomics, etc.) or by species.

```txt
    expression_data/
    |---- RNA-seq_experiments/
          |---- dataset1.txt
          |---- dataset2.txt
          |---- dataset3.txt
          |---- dataset3.txt
    |---- Proteomics_experiments/
          |---- dataset1.txt
          |---- dataset2.txt
          |---- dataset3.txt
```

Place your expression data files in the `expression_data` folder, as tab delimited text files with normalized data for each replicates in the columns (header), and each gene in the rows (first column), as shown in the example.

> [!IMPORTANT]
>The files must be tab‑delimited text.<br>
 All replicates should have the same name in the header to be grouped together (For example: leaf, leaf, leaf, root, root, root, heat, heat, heat, etc.).

```txt
Gene ID	Leaf	Leaf	Leaf	Fruit	Fruit	Fruit	Root	Root	Root
gene1	156.22	119.92	105.89	54.95	39.59	24.56	40.43	8.32	44.93
gene2	49.33	6.45	26.7	11.97	0.85	10.61	21.25	1.54	18.9
gene3	10.84	6.06	9.98	13.59	8.37	11.23	10.11	9.68	10.89
```

Also, just by placing the expression files in the `expression_data` directory will make available those datasets in the expression tools.


### expression_info.json

The JSON file `egdb_files/json_files/tools/expression_info.json` includes:

- <span id="link">`link` </span> - links to the gene annotation page for each dataset (it is also possible to add external links or remove links) in the *Average values* table.  By default this field should remain empty (`"link":""`). Using  `"link":"#"` field will remove links. To use an external link we just need to add the URL include the word `query_id` in the place of the gene identifier. For example, for UniProt the link would be `http://www.uniprot.org/uniprot/query_id` and the gene IDs in the expression matrix should correspond to UniProt identifiers.

- `annotation_file` - field it is possible to provide an annotation file to add gene annotations in the *Average values* table and links to the gene annotations pages.

-  `description` - field than add a dataset description. It will include the indicated PHP file, which should be placed in `egdb_custom_text/expr_datasets/` within the your `egdb_files` folder. It is recommendable to describe briefly the experimental conditions and links to its publication. 
The descriptions of all datasets will be shown in the *Expression tools*, which can be enabled using the variable `$expr_menu` in the configuration file (*easyGDB_conf.php*).


- `images` - In case of enabling the expression card visualization, it is important to add the file names of the images used for each sample. In that case, you can add image files in the images path (`egdb_images/expr/` in your `egdb_files` folder) and add the names of the sample with their corresponding image. 
  >[!IMPORTANT] 
  >The sample name in the JSON is identical to the sample name in the header of the tab-delimited expression data file, and the image file name corresponds with the name in the images path. The expression cards could be useful to show real photos of the sample phenotype together with its expression data.

<br>

- `cartoons` - Expression cartoons JSON file that associate each sample with its corresponding cartoon image. [configuration](#cartoons)


``` json
  {
  "Example1 - Plant_gene_expression_RPKM.txt":
    {"link":"",
      "annotation_file":"example/gene_annotations.txt",
      "description":"example1_description.php",
      "images":
        {
          "Leaf":"leaf.jpeg",
          "Fruit":"fruit.jpeg",
          "Root":"root.jpeg",
          "Peel":"peel.jpeg",
          "Seed":"seed.jpeg",
          "Germinating Seed":"germinating_seed.jpeg",
          "Darkness":"darkness.jpeg",
          "Drought":"drought.jpeg",
          "Heat":"heat.jpeg",
          "Cold":"cold.jpeg"
        }
    },
  "Example2 - Organism dataset name.txt":
    {"link":"#",
      "description":"example2_description.php"
    },
  "Example3 - Dataset with cartoons.txt":
    {
      "link":"",
      "annotation_file":"example/gene_annotations.txt",
      "cartoons":"cartoons_example1.json"
    },
  "Example4 - Other dataset.txt":
      {
        "link":"http://www.uniprot.org/uniprot/query_id"
      }
  }
```

<br>

### Cartoons
For the configuration of cartoons we should provide a separated JSON file (`json_files/tools/cartoons_example1.json` in your `egdb_files` folder). It is possible to add as many cartoon JSON files as datasets are with cartoons.

If expression cartoons are enabled we should provide a JSON file to set up each cartoon image, the samples associated with them, and their dimensions and coordinates.
Cartoon images should be placed at `egdb_images/expr/cartoons/` inside your `egdb_files` folder, which usually correspond to your project customization folder and it could have been renamed before (see [Customize file paths](#customize-file-paths)).

>[!NOTE]
A simple way to create the cartoons is to generate a whole picture using one layer for each tissue. Then, each image should be generated with a transparent background and including only the current tissue colored in white (the code will replace white by the expression color for each sample). Exporting one image file for each sample, all with the same dimensions. That way, when all cartoon images are overlapped they will form the whole picture.
Drawings in the separate images should not overlap, and black color should be used for drawing borders, captions, arrows or other elements than the actual expression tissue.


``` JSON
{
  "cartoons":
    [
      { "img_id":"t1",
        "sample":"sample1",
        "image":"tissue1.png",
        "x":10,
        "y":10,
        "width":250,
        "height":300
      },
      { "img_id":"t2",
        "sample":"sample2",
        "image":"tissue2.png",
        "x":10,
        "y":10,
        "width":250,
        "height":300
      },
      { "img_id":"t3",
        "sample":"sample3",
        "image":"tissue3.png",
        "x":10,
        "y":10,
        "width":250,
        "height":300
      },
      { "img_id":"t4",
        "sample":"sample4",
        "image":"tissue4.png",
        "x":10,
        "y":10,
        "width":250,
        "height":300
      }
    ]
}
```

## Customitation

### Customise the expression color scale
To customise the general expression color scale, which will then be used in the expression tools, in  the `easyGDB_conf.php` file you can define the colors, ranges and labels.
The order of the elements is from <b>lowest</b> to <b>highest</b> expression.

``` php
$colors = ["#eceff1","#b3e5fc","#80cbc4","#ffee58","#ffb74d","#ff8f00","#ff4f00","#cc0000","#D72C79","#801C5A","#6D3917","#443627"];
$ranges_text =["<1",">=1",">=2",">=5",">=10",">=50",">=100",">=200",">=500",">=1000",">=5000",">=8000"];
$ranges=[[0,0.99],[1,1.99],[2,4.99],[5,9.99],[10,49.99],[50,99.99],[100,199.99],[200,499.99],[500,999.99],[1000,4999.99],[5000,7999.99],[8000,80000]];
```

`colors`: place the color code in hexadecimal format that you want to use for each expression range.

`ranges_text`: write the text to be displayed in each of the expression ranges.

`ranges`:  array that contains the lower and upper limits of each range are defined. [low,up].

If you want to create a specific color palette for each dataset, you can configure it in the `expression_info.json` file located in the `egdb_files/json_files/tools` folder and there put the variables shown above in the section of each dataset with the keyword "expression_colors" like this.

``` JSON
{
 "Example3 - Dataset with cartoons.txt":
    {
      "link":"",
      "annotation_file":"example/gene_annotations.txt",
      "cartoons":"cartoons_example1.json",
      "expression_colors":
      {
       "colors":["#c8c8c8","#f0c320","#ff8800","#ff7469","#de2515","#b71005","#0bb4ff","#4c2882"],
       "ranges_txt":["<1",">=1",">=2",">=5",">=10",">=50",">=100",">=200"],
       "ranges":[[0,0.99],[1,1.99],[2,4.99],[5,9.99],[10,49.99],[50,99.99],[100,199.99],[200,49999.99]]
      }
    }
}
```

>[!NOTE]
>In case of not adding these variables or any error defining them in the `easyGDB_conf` and/or in the `expression_info.json` , a default color palette will be loaded.

### Customise the visualization methods
To customize the visualization methods in the Expression results it is possible to edit the values of the variable `$positions` in the `easyGDB_conf.php` file. Set the value of any tool to 0 to disable it, and to 1 or any number greater than 1 to enable it and set the order in which they will appear in the graphical interface, starting for 1 on top of the output page and adding below the next visualization methods as the values increase.

```php
// Expression tools order: 0 for not shown, >=1 to setup the order
$positions=[
  'description' => 1,
  'cartoons' => 2,
  'lines' => 3,
  'cards' => 4,
  'heatmap' => 5,
  'replicates' => 6,
  'table' => 7
];
```
<!-- ## Expression vierwer
El apartado [configuration de expression tools](#configuration) muestra todo el proceso para poner en funcionamintento esta herramienta -->
## Expression comparator
Its main configuration is the same as in the [Expression Viewer](#expression-viewer), but this tool also includes specific functionalities that are configured independently:

- `comparator_link.json` - 
File located in `egdb_files/json_files/tools/`. It works the same way as the [link](#link) configuration used in the **Expression Viewer**, but this file defines the link to the gene annotation page for all genes in the selected datasets.

  ``` json
  "link":"#"
  ```

- `comparator_lookup.json` file located in `egdb_files/json_files/tools/`- List of gene identifiers to the equivalent list of genes in a different annotation version, or equivalent orthologs in other species. 

  ``` json
    "gene1.1":"new_gene1.1",
    "gene2.1":"new_gene2.1",
    "gene3.1":"new_gene3.1",
    "gene4.1":"new_gene4.1",
  ```

- A `comparator_gene_list.txt` file may be added to provide a gene list to the tool’s input section and an autocompletation gene search. *(Optional)*

    ```txt
        expression_data/
        |---- RNA-seq_experiments/
              |---- dataset1.txt
              |---- dataset2.txt
              |---- dataset3.txt
              |---- dataset3.txt
        |---- Proteomics_experiments/
              |---- dataset1.txt
              |---- dataset2.txt
              |---- dataset3.txt
        |--- comparator_gene_list.txt
    ```


## Coefficient of variation calculator
This tool calculates the coefficient of variation for the different genes included in the atlas. 
It uses the same [datasets](#required-files-and-folder) employed by the *Expression Viewer* and *Expression Comparator* modules.

The configuration of the *Coefficient of Variation Calculator* is defined in the [expression_info.json](#expression_infojson) file, requiring only the `expression_file_name.txt` and its associated `link`.

## Co-expression search

Correlation data should be placed in the `$coexpression_path`, in `$root_path/coexpression_data` by default (defined as `$coexpression_path = "$root_path/coexpression_data"` in *easyGDB_conf.php*)

>[!IMPORTANT]
>These files should be tab-delimited text files.

This tool takes two mandatory elements: 

* A **`.txt`** file containing the output of a co-expression analysis ([WGCNA](https://doi.org/10.1186/1471-2105-9-559), for example) that consists of two columns: 
1. Gene IDs.
2. Cluster names where that gene belongs.

For example:

``` txt
gene_ID  cluster
Gene1    brown
Gene2    brown
Gene3    black
Gene4    purple
Gene5    black
Gene6    purple
Gene7    black
Gene8    brown
Gene9    black
Gene10   black
Gene11   brown
Gene12   purple
```

* A correlation matrix of all genes from each cluster, preferably filtered to only show correlation values above a certain threshold. These matrices must be gzipped and named after the name of the cluster (e.g. `black_cor.tsv.gz`).

Black cluster correlations:
``` txt
      Gene3    Gene7   Gene9
Gene3   1      0.841   NA
Gene7   NA     1       0.938
Gene9   NA     NA      1
```

These two elements must be added to the same new folder with a name of your choice inside the your coexpression folder `(coexpression)`.

```
correlation/
    |---- example/
          |---- gene_cluster.txt
          |---- black_cor.tsv.gz
          |---- brown_cor.tsv.gz
          |---- purple_cor.tsv.gz
```

You can use the code from this [github](https://github.com/Javiersdr/Co-expression_analysis) to obtain an example. First, you must execute the ```configure_WGCNA.R``` script. Then, with ```cor_matrix_filter.R``` you can calculate the correlations for easyGDB.


Optionally, if you have an annotation file for those genes, you can add it to the JSON folder in `egdb_files/json_files/` and then create a `coexpression.json`, where the path to the annotations file name is connected to the name you gave to your dataset in correlations, like in the other tools.

```
01 Example dataset: "Example/example_annotations.txt"
02 Example2 dataset: "Example2/example2_annotations.txt"
```

<br><br>

# Passport and phenotype tools

This module provides management of passport and phenotypic data and integrates tools such as:

- ***Map navigation*** : lists available species and plots accessions on a world map using coordinates or country/country code. It helps users quickly visualize the geographic distribution of collections and explore species by location.

- ***Passport and phenotype search*** : search accessions by passport fields or phenotype traits, so users can quickly find specific samples.

- ***Phenotype extraction*** : extract phenotypic data and download it as a CSV file for use in association analyses such as GWAS. The tool shares its input files with the tools described earlier.

## <h2 id="easyGDB_conf">Configuration</h2>
Each *Passport and phenotype tools* can be included or hidden in the toolbar.<br>
Set each variable to `1` or `0` to enable or disable the corresponding tool in `easyGDB_conf.php` (`egdb_files/egdb_conf/easyGDB_conf.php`):
 <!-- [easyGDB_conf.php](https://github.com/noefp/easyGDB_docker/blob/main/src/egdb_files/egdb_conf/easyGDB_conf.php) : -->

- `tb_passport` - Enable the ***Passport and Phenotype***  dropdown in the toolbar.
  - `tb_navigation` - Includes the ***Map Navigation*** link in the toolbar.
  - `tb_search_passport` - Includes the ***Passport and phenotype search*** link in the toolbar.
  -  `$tb_phen_ex` - Includes the ***Phenotype extraction***  link in the toobar.
  
- To enable the tool’s features, you must set the following variables:  
  - `show_qr` -  Enable a ***QR code*** containing the URL with the information of the selected accession.
  - `show_map` -  Enable the ***map***  displaying the location of the selected accession.

Paths required:

- `$images_path` = path where the images used by the tool will be stored (by default `/egdb_files/egdb_images`).

- `$passport_path` = path where the passport and phenotype files will be stored (by  default `/passport`).

- `$custom_css_path`= path to css file (`$egdb_files/css/file_name.css`). Only if you want to customize the visual styling of the tool’s.

<br>

### <h3 id="files_and_folders">Required files and folders </h3>

Passport and phenotype datasets and configuration files should be placed in the `passport` folder  in `$root_path/passport` by default (defined as `$passport_path` = `$root_path/passport/{folder_name}` in *egdb_conf/easyGDB_conf.php*). 
The passport data folder should be organized as shown below:

```txt
    $passport_path/
    |---- Species_1/
          |---- passport.json
          |---- passport_data.txt
          |---- phenotype_data.txt
    |---- Species_2/
          |---- passport.json
          |---- passport_data.txt
          |---- phenotype_data.txt
          |---- phenotype_data2.txt
          |---- phenotype_data3.txt
          
    |---- germplasm_list.json
```


> [!IMPORTANT]
>The files must be tab‑delimited text and may contain as many columns as required.

> [!NOTE]
> [Here](https://github.com/noefp/easyGDB_docker/tree/main/src/passport/example) you can find  example files that illustrate the structure and configuration required for the tool to function properly.

### <i>passport_data.txt file</i>
  This file contains all the necessary data related to the passport (geographical location, species, and any type of information that is not phenotypic).

  | ACC_name | Country | Country code | Latitude | Longitude | ... |
  | --- | --- | --- | --- | --- | --- |
  | Hass |  | USA |  |  |
  | Picual | Spain | ESP | 36.735 | -3.68972 |
   | Yummy | China | CHN |  |  |
  
> [!IMPORTANT]
> ***Latitude and Longitude***,  or ***Country***  or ***Country code*** ( [Alpha3-code](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-3) ), are required to position the markers on the maps, and they must be written exactly as shown in the example table. *Providing any one of these options is sufficient*.

<br>

### <i>phenotype_data.txt file</i>
  This file (there may be more than one) contains all the phenotypic traits of the species, with each phenotype represented separate column.
  Each dataset may include different phenotypic traits, depending on the information recorded.

  | ACC_name | phenotype_1 | phenotype_2 |...|
  | --- | --- | --- | --- |
  | Hass | green | 10 |
  | Picual | purple | 12.54 |
  | Yummy | red | 33 |
<br>
<!-- <h3>The <b>Navigation</b> section of the toolbar:</h3> -->
<!-- Hablar un poco cual es la funcion de est aseccion. -->

### <i>germplasm_list.json file</i>

If there is more than one species organized into different subdirectories (Species_1, Species_2, …), and you want to display those subdirectories in a menu, you must include this file.  
The structure and configuration of these subdirectories are defined in the *germplam_list.json* file, as show below:

  ```JSON
  {
  "annona":
    {"sps_name":"Annona cherimola",
      "common_name":"Cherimoya",
      "image":"sp1.png",
      "public":"1"
    },
  "mango":
    {"sps_name":"Mangifera indica",
      "common_name":"Mango",
      "image":"mango.png",
      "public":"1"
    }
  }
  ```
  `sps_name`: The scientific name of the species.<br>
  `common_name`: The common name of the species.<br> 
  `image`: Representative image of the species (stored in *egdb_images/species/*, e.g., egdb_images/species/sp1.png).<br>
  `public`: Indicates whether the species is public or private.

  ![subdirectori_map.png](/_resources/subdirectori_map.png)
  
> [!NOTE]
> If only **one species is available**, you do not need to include the file (germplasm_list.json). Without this file, the tool will simply  display all the information into de folder for that single species and its location on the map.

<br>

### <i>passport.json file</i>

  This JSON file defines all the configuration settings required for the tool to operate.
  ```json
  {
    "passport_file":"passport_data.txt",
    "acc_link":"ACC name",
    "phenotype_files":["phenotype_data.txt","phenotype_data2.txt"],
    "phenotype_imgs":"phenotype_imgs.json",
    "img_src_msg":"<p>The images used in the Phenotype descriptors come from x et al.</p>",
    "phenotype_file_marker_trait":"sp2_phenotype_data.txt",
    "marker_column":"column_index",
    "sp_name":"Specie",
    "convert_to_cathegoric":"convert_numeric_to_cathegoric.json",
    "translator":"translator.json",
    "featured_descriptors":"featured_descriptors.json",
    "numerics_columns_without_average":
    {
        "sp_phenotype_1.txt":[1,2],
        "sp_phenotype_2.txt":[3]
    },
    "hidden_search_traits":
    {
        "passport_data.txt":[1],
        "sp_phenotype_1.txt":[2,3],
        "sp_phenotype_2.txt":[3,4]
      }
  }
  ```
<p style= font-size:17px><b><i>Basic fields</i></b></p>

`passport_file`: name of the passport data file (e.g., sp_passport.txt). Must be a single file. $\color{yellow}{(madatory)}$

`acc_link`: defines which column (name) of the passport data file will be the unique identifier to use as link to access the information (usually the accession name or similar identifier). $\color{yellow}{(mandatory)}$

> [!IMPORTANT]
    The data in the column selected cannot contain accents or special characters.

`phenotype_files`: list the phenotype data files.

>[!NOTE]
  The file name determines the section name displayed in the output.

`sp_name`: species name than determines configuration of the [map markers](#map-markers-icons) name and the [gallery](#Gallery) folder. $\color{yellow}{(mandatory)}$

>[!NOTE]
If this variable is empty or not defined in the configuration, it will affect the configuration of the map markers, the gallery, and various sections of the tool.

<br><p style= font-size:17px><b><i>Avanced fields</i></b></p>

`phenotype_imgs`: JSON file associating each trait with the corresponding picture. [[Configuration]](#phenotype_imgs-json-file-configuration)

`img_src_msg `: text string used to reference the image source in the output.

>[!NOTE]
   This text will appear at the bottom of the page.

`convert_to_cathegoric`: JSON file than convert numeric scale trait values to categorical classes within defined ranges. For example, fruit size values between 0–2 cm may be classified as “small”. [[Configuration]](#convert_to_cathegoric-json-file-configuration)

 > [!NOTE] 
  *Non‑numeric* (categorical) phenotypic traits are not configured in this file.

`translator`: JSON  file to add additional languages to phenotypes names. [[Configuration]](#translator-json-file-configuration)

`featured_descriptors`: file to highlight a set of important traits. [[Configuration]](#featured_descriptors-json-file-configuration)

`phenotype_file_marker_trait` and `marker_column`: name of the dataset and index column from which the phenotypic traits used for map markers. [[Configuration]](#map-markers) 

> [!NOTE]
    If the species does not have multiple phenotypic traits, this parameter is not required.<br>
    If only a single image is used for all accessions, this parameter should be left empty.

`numerics_columns_without_average`: specifying which numeric phenotype traits should not be averaged.<br>Each dataset configuration file must explicitly define the columns that are excluded.

> [!NOTE]
  Be sure to specify in this section any columns with dates or numeric information that cannot be used to calculate averages.<br>
  >The system uses natural numbers, the first column is column 1, not column 0 ( 1 = first column ).
  <br>

`hidden_search_traits`: to control which phenotype traits are available for selection in the Advanced Search interface and in phenotype extraction tool , each dataset configuration file must explicitly define the columns that should not appear as selectable options.

> [!NOTE]
> The system uses natural numbers, the first column is column 1, not column 0.
<br>

---
### <h3 id="map-markers-icons">`map markers`</h3>
Map markers are the icons used to show each accession on the map. The tool can use a different marker image depending the phenotype trait value. Each marker image (icon) filename is constructed as: `sp_name` + _ + `traitValue`.png. 

If no specific marker is available for a trait value,  the tool will use the default marker image:` sp_name`+*_default.png*. 

> [!IMPORTANT]
All images used for map visualization must be stored in the ***$images_path/map_labels*** directory.<br>
Example directory structure:
```txt
  $images_path/
  |---- map_labels/
        |---- Species_1_traitValue1.png
        |---- Species_1_traitValue2.png
        |---- Species_1_default.png
        
  ```
> [!NOTE]
 The keys `phenotype_file_marker_trait`  and  `marker_column` (in *passport.json*) determine which trait is used to select the marker image.<br>
  If you want to use a single marker for all accessions of a species, you may remove or leave empty the `phenotype_file_marker_trait`  and  `marker_column` variables.
  In this mode,the tool will always use the default marker image (`sp_name`+*_default.png*).

  For example, the images below would be: *Species_3_green.png, Species_3_purple.png, etc*.

  - passport.json

  ```json
  { ...

    "phenotype_file_marker_trait":"sp3_phenotype_data.txt",
    "marker_column":2,
    "sp_name":"Specie_3",
    
    ...
  }
  ```

 - *sp3_phenotype_data.txt*

    | ACC_name | Color | Thickness skin | Consistency | Adherence skin/flesh | Main color flesh |
    | --- | --- | --- | --- | --- | --- |
    | ACC1 | green | 1.51 | leathery | weak | cream |
    | ACC2 | purple | 0.89 | leathery | weak | cream |
    ...
    <br>  

<!-- ![images_sps.png](../../_resources/images_sps.png) -->
  ![images_sps.png](/_resources/images_sps-1.png)
  ![map.png](/_resources/map.png)

<br>

### `featured_descriptors` (json file configuration)
Selection of the phenotypic traits of the species that will be highlighted.<br>
Specify the phenotype data file and provide a list containing the names of the phenotype:
```json
{
  "phenotype_data.txt":
  [
    "phenotype_1"
  ],
  "phenotype_data2.txt":
  [
    "phenotype_1",
    "phenotype_2",
    "phenotype_3"
  ]
}
```
For example:
```json
{
  "Ripe_fruit_descriptors.txt":
  [
    "Color",
    "Thickness skin",
    "Consistency",
    "Adherence skin/flesh",
    "Main color flesh"
  ]
}
```
*Ripe_fruit_descriptors.txt*:
<!-- ![featured_table.png](../../_resources/featured_table.png) -->
| ACC_name | Color | Thickness skin | Consistency | Adherence skin/flesh | Main color flesh |
| --- | --- | --- | --- | --- | --- |
| ACC1 | medium_purple | 1.51 | leathery | weak | cream |
| ACC2 | medium_purple | 0.89 | leathery | weak | cream |


Section of the accession information page:<br>
![Featured_traits .png](/_resources/featured_traits_gallery.png)

<br>

### `translator` (json file configuration)
Translates and adds additional languages to the specified phenotype trait.<br>
Specify the phenotype data file and provide the names of the phenotypes that will be translated for the primary descriptor and the secondary descriptor, if added.
```json
"phenotype_data.txt":
    {
    "phenotype_1":
        {
            "primary_descriptor":"Phenotype number one",
            "secondary_descriptor":"Fenotipo número uno"
        },
        "phenotype_2":
        {
            "primary_descriptor":"Phenotype number two",
            "secondary_descriptor":"Fenotipo número dos"
        },
        },
```
For example:
```json
"Leaf_descriptors.txt":
    {
    "Length (cm)":
        {
            "primary_descriptor":"Length of leaf blade (cm)",
            "secondary_descriptor":"Longitud de la hoja"
        },
     "Width (cm)":
        {
            "primary_descriptor":"Width of leaf blade (cm)",
            "secondary_descriptor":"Anchura de la hoja"
        },
        },
```
*Leaf_descriptors.txt*:
<!-- ![traslator_table.png](../../_resources/traslator_table.png) -->
| ACC_name | Length (cm) | Width (cm) |
| --- | --- | --- |
| ACC1 | 16.7 | 6.1 |
| ACC1 | 17.1 | 5.8 |
| ACC1 | 13.84 | 6.04 |

Traduction:<br>
![traslator.png](/_resources/traslator.png)

<br>

### `convert_to_cathegoric` (json file configuration)

Specify the name of the phenotype data file. Inside it, the phenotypics traits (numerics) are defined, each with two elements:
- ***"ranges"***: a list of numerical intervals expressed as strings in the format "min-max".
- ***"categories"***: a list of textual labels associated with each corresponding range.
```json
{	
    "phenotype_data.txt":
    {
        "phenotype_2":
        {
            "ranges":["min-max","min-max","min-max"]
            "categories":["value1","value2","value3"]
        }
    }
}
```
For example:
```json
{
    "Leaf_descriptors.txt": 
    {
        "Length (cm)":
            {
                "ranges": ["11.5-13.8","13.8-18.4","18.5-20.6","20.7-22.90"],
                "categories": ["short","short-medium","medium","medium-long","long"]
            },
        "Width (cm)":
            {
                "ranges": ["4.8-5.7","5.71-6.59","6.6-7.6","7.61-8.49","8.5-11.3"],
                "categories": ["narrow","narrow-medium","medium","broad","very_broad"]
            },
}
```
*Leaf_descriptors.txt*:
<!-- ![traslator_table.png](../../_resources/traslator_table.png) -->
| ACC_name | Length (cm) | Width (cm) |
| --- | --- | --- |
| ACC1 | 16.7 | 6.1 |
| ACC1 | 17.1 | 5.8 |
| ACC1 | 13.84 | 6.04 |

> [!IMPORTANT]
The selected phenotype must be numeric.

convert to categoric:<br>
![ranges.png](/_resources/ranges.png)

<br>

### `phenotype_imgs` (json file configuration)
Defines the images of the different phenotypic traits values of the species and groups them to be displayed.<br>
>[!IMPORTANT]
The images used for this section must be stored in the ***$egdb_images/descriptors_imgs/{species_name}*** directory.
```txt
$egdb_images/
|---- descriptors_imgs/
      |---- Species_1/
            |---- example_trait1.png
            |---- example_trait2.png
            |---- example_trait3.png
            |---- example2_help.png
          
```
The JSON file contains the name of the phenotype data file (e.g. phenotype_data.txt). Inside it, the phenotypics traits are defined, each with two elements:
- `img_name` : the base name of the image file, followed by "_value.png".
- `options`: list of the possible traits values. (If the values contain spaces, you must replace them with underscores "_")
```json
"phenotype_data.txt"
{
    "phenotype_2":
    {
        "img_name":"example_value.png",
        "options":["trait1","trait2","trait3"]
    }
}
```

The images for the phenotypic trait `phenotype_2` from `phenotype_data.txt` would be:
`example_trait1.png, example_trait2.png, example_trait3.png ` 

For example:
```json
{
    "Leaf_descriptors.txt":
    {
        "Length (cm)":
        {
            "img_name":"sagarpa_leaf_blade_length_value.png",
            "options":["very_short", "short", "medium","long","very_long"]
        },
        "Width (cm)":
        {
            "img_name":"sagarpa_leaf_blade_width_value.png",
            "options":["very_narrow","narrow","medium","broad","very_broad"]
        },
    }
}

```
>[!IMPORTANT]
 For phenotypes traits numeric this configuration depends on *convert_to_categoric.json*, which defines the [categories](#convert_to_cathegoric-json-file-configuration) for each `options`.


>[!NOTE]
If a phenotypic trait value matches one `options`, that trait image is highlighted.

![phenotype_img.png](/_resources/phenotype_img.png)

<!-- ![phenotype_img.png](../../_resources/phenotype_img.png) -->

If you want to add an image to illustrate the phenotype in a general way, the image filename must end with _help. This will automatically display the selected image with the message “help image” below it.
For example:
```json
"Inflorescence_descriptors.txt":
{
    "Flowering type":
    {
      "img_name":"sagarpa_inflorescence_flowering_type_help.png",
      "options":[""]
    }
}
```
![Help_image.png](/_resources/Help_image.png)

<br>


<h3 id="Gallery">Gallery</h3>

The tool includes a gallery section used to display images of the different varieties of each species.<br>
>[!IMPORTANT]
To enable this functionality, you must create a directory inside ***$egdb_images/gallery*** named after the species.
Inside that directory, create subdirectories named after each variety of the species, and place the images inside their corresponding subdirectory.

```txt
$egdb_images/
|---- gallery/
      |---- Species_1/
            |---- ACC1/
                  |---- image1.png
                  |---- image2.png
            |---- ACC2/
                  |---- image1.png
                  |---- image2.png
```

![Gallery.png](/_resources/gallery.png)

<br>

<h2 id="custom"> Customization Passport and Phenotype tools</h2>

This section defines the visual appearance of the tools.

<h4>Collapsible components:</h4>

  - Featured Traits & Gallery:<br>

  ![Featured_traits_collapse.png](/_resources/Featured_traits_collapse.png)
-  phenotypic trait sections:<br>

  ![Phenotypic_traits_collapse.png](/_resources/Phenotypic_traits_collapse.png)
-  categorical information icons:<br>

  ![info_icon.png](/_resources/info_icon.png)

>[!IMPORTANT]
These appearance parameters are configured in the CSS file located in the root directory defined in easyGDB_conf.php.<br>
*$custom_css_path = "$egdb_files_folder/css/name_file.css"*<br>
Ensure that The ***!important*** flags in the CSS file are applied correctly

  - background → background color.
  - color → text color.
  - :active → style applied while the element is being pressed.



<h3>Default appearance:</h3>

 - phenotype trait sections and Featured Traits & Gallery:

    ```css
    .phenotype_traits, .phenotype_traits:active {
        background: #76a835 !important;
        color: white;
    }
    ```
- categorical information icons
    ```css
    .categoric_info, .categoric_info:active {
        background-color: #7a3;
        color: white;
    }

<h4>When the user hovers over:</h4>

 - phenotype trait sections and Featured Traits & Gallery:

    ```css
    .phenotype_traits:hover {
        background: #8b4 !important;
        color: white;
    }
    ```
- categorical information icons
    ```css
    .categoric_info:hover {
        background-color: #90be52;
        color: white;
    }
    ```
<br>
<h3>Icons configuration (Phenotype extration input)</h3>

The icons displayed in the different sections of the tool can be customised by editing the file `custom_tools_icons.json` located at `egdb_files/json_files/tools/custom_tools_icons.json`.

```json
{
    "SPECIES_ICONS": {
        "mango":   "fa-seedling",
        "avocado": "fa-seedling",
        "annona":  "fa-seedling",
        "lemon":   "fa-lemon",
        "citrus":  "fa-lemon",
        "apple":   "fa-apple-alt",
        "carrot":  "fa-carrot",
        "pepper":  "fa-pepper-hot",
        "berry":   "fa-holly-berry",
        "coffee":  "fa-mug-hot",
        "papaya":  "fa-seedling"
    },
    "SPECIES_ICONS_DEFAULT": "fa-seedling",
    "DATASET_ICONS": {
        "ripe_fruit":    "fa-lemon",
        "mature_fruit":  "fa-lemon",
        "fruit":         "fa-lemon",
        "leaf":          "fa-leaf",
        "leaves":        "fa-leaf",
        "inflorescence": "fa-spa",
        "flower":        "fa-spa"
    },
    "DATASET_ICONS_DEFAULT": "fa-flask",
    "TRAIT_ICONS": {
        "weight":  "fa-weight",
        "height":  "fa-ruler-vertical",
        "width":   "fa-ruler-horizontal",
        "length":  "fa-ruler-horizontal",
        "color":   "fa-palette",
        "colour":  "fa-palette",
        "shape":   "fa-shapes"
    },
    "TRAIT_ICONS_DEFAULT": "fa-leaf"
}
```

The `custom_tools_icons.json` file supports the following parameters:

- **`SPECIES_ICONS`** — icons assigned to individual species, matched by keyword against the species folder name.
- **`SPECIES_ICONS_DEFAULT`** — fallback icon used when no species keyword matches.
- **`DATASET_ICONS`** — icons assigned to individual datasets (tissues or developmental stages), matched by keyword against the dataset key.
- **`DATASET_ICONS_DEFAULT`** — fallback icon used when no dataset keyword matches.
- **`TRAIT_ICONS`** — icons assigned to phenotypic traits, matched by keyword against the trait column name.
- **`TRAIT_ICONS_DEFAULT`** — fallback icon used when no trait keyword matches.

To add a new icon, add a keyword and its corresponding FontAwesome 5 class. Keywords are matched case-insensitively against names, so a keyword `"weight"` will match any trait whose name contains the word "weight".

If the user do not wants icons in your installation, can either delete the `custom_tools_icons.json` file or remove the individual parameters you do not need. The tool will fall back to the default icons or hide icon elements gracefully.
<br><br>


## Gene variation tools

Work in progress. Available soon.

# JBrowse

An example of JBrowse is already implemented but when you want to include the genome browser for your species of interest you can find more information in the JBrowse manual (<http://gmod.org/wiki/JBrowse_Configuration_Guide#prepare-refseqs.pl>). Additionally, below you can find some suggestions.

Open a terminal using docker-compose or Docker desktop

    docker-compose exec easy_gdb /bin/bash

Upload your sequences to JBrowse. This is how the gene models were uploaded in the example:

    jbrowse$ bin/prepare-refseqs.pl --fasta ../easy_gdb/templates/jbrowse_example_data/genome.fasta --out data/easy_gdb_sample
    jbrowse$ bin/flatfile-to-json.pl -gff ../easy_gdb/templates/jbrowse_example_data/gene_models.gff --key "EasyGDB gene models" --trackLabel egdb_gene_models --trackType CanvasFeatures --type mRNA --out data/easy_gdb_sample
    jbrowse$ bin/generate-names.pl --tracks egdb_gene_models --out data/easy_gdb_sample/

When adding new tracks, edit the file `data/easy_gdb_sample/trackList.json` to customize them in JBrowse. Below there is an example of the gene model track with a link to the database (`url`).

``` json
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

To allow multiple genome browser species, accessions or versions we need to modify the file `data/easy_gdb_sample/tracks.conf` to include the folder name where the data are stored (remember the jbrowse folder name in import_genes.pl)

    [general]
    dataset_id = easy_gdb_sample

In the file `jbrowse/jbrowse.conf` we can include as many species as we want. It is possible also to include external links in the URL field. Here we use the easy GDB example and the volvox and yeast examples from JBrowse:

    [datasets.easyGDB]
    url  = ?data=data/easy_gdb_sample
    name = Easy GDB Example

    [datasets.volvox]
    url  = ?data=sample_data/json/volvox
    name = Volvox Example

    [datasets.yeast]
    url  = ?data=sample_data/json/yeast
    name = Yeast Example



## Private application

In the file `apache/easy_gdb_apache.conf` we are overwriting the apache configuration inside the Docker repository. There is a block of code that is commented out. If you want to have a private genomics database you can enable that piece of code to make everything private in /var/www/html/.

        <Directory "/var/www/html">
            AuthType Basic
            AuthName "Restricted Content"
            AuthUserFile /etc/apache2/.htpasswd
            Require valid-user
        </Directory>

Create the first user to access private data (Create the passwdfile. If passwdfile already exists, it is rewritten and truncated.) 

    htpasswd -c /etc/apache2/.htpasswd First_user

Add more users 

    htpasswd /etc/apache2/.htpasswd another_user

### Start local server

In many cases, after applying some changes you will need to restart the server to make the changes effective. In a local installation you can stop the application and them start it again from the terminal using the next command:

``` bash
php -S localhost:8000
```

Or restarting the docker-compose service when using the Docker container.

In a server:

``` bash
sudo service apache2 restart
```


# Set up EasyGDB PostgreSQL database (optional, not recommended)

## Install PostgreSQL

Already installed in the Docker container.<br>

 To install Postgres in Linux you can follow the instructions at: <https://www.postgresql.org/download/linux/ubuntu/>

The next commands worked well at the time this documentation was written:

``` bash
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -

sudo apt-get update
sudo apt-get -y install postgresql
sudo apt-get install php-pgsql
```


## Set up EasyGDB PostgreSQL database in Docker 

There is an option to set up gene annotations in a PostgreSQL relational database. Although it is not needed in recent versions of EasyGDB, performance does not make a difference. This method is no longer maintained.

We need to set up the database so the easy GDB code is able to find it. Remember to change the password by the password you will use for web_usr [below](#create-a-new-role-db-user)

open the file `egdb_files/egdb_conf/database_access.php`.

And setup the database connection based on the user, password and database name you used. The host is `DB` for the Docker installation and `localhost` for linux installations in servers or personal computers:

``` php
function getConnectionString(){return "host=DB dbname=annot1 user=web_usr password=password";};
```

Or in the case you will not use the relational database (for gene annotations):

``` php
function getConnectionString(){return null;};
```

> If not using the Docker container the host for the postgreSQL database is usually `localhost`

## Set up password in PostgreSQL

[in the Docker container you already have a postgres password defined]

Open a terminal using docker-compose, docker exec or Docker desktop

    docker-compose exec DB /bin/bash

enter the postgres console:

    psql -U postgres

or

    docker exec -ti DB psql -U postgres

You can use `\q` to exit the PostgreSQL console or exit to leave the Docker bash console.

To change the password for the postgres user:

``` sql
\password postgres
You will be asked to type your new password
\q
```

## Create a new database

Here, we will create a new database `annot1`. Any time you want to create a new database to test some data or new versions, you can create a new one and point to it in the file `egdb_files/egdb_conf/database_access.php`.

Open a terminal using docker-compose, docker exec or Docker desktop if you need to and create a new database:

``` sql
CREATE DATABASE annot1;
\l
\q
```

## Create a new role (DB user)

It is recommended to use a different user than postgres to access the database (it will have limited control). Here, we will create the user `web_usr`. Note that in this example the password you type will be visible in the terminal, and the history, so we will create a temporary password and then we will change it in the next step.

Open a terminal using docker-compose, docker exec or Docker desktop if you need to and create a new database:

``` sql
CREATE ROLE web_usr WITH LOGIN ENCRYPTED PASSWORD 'tmp_password' CREATEDB;
\password web_usr
type a new password
\q
```

## Import annotations

Now we should have an empty database called `annot1` created. With this command we will create the database schema:

    docker exec -i DB psql --username postgres annot1 < src/easy_gdb/scripts/create_annot_schema2.sql

Then, we will import annotations to the database. First we will import all the gene names, for that we will need a file such as `easy_gdb/templates/annotations/gene_list.txt` with all the gene identifiers from our organism. It is recommended to use the transcript name (gene1.1).

We will import all the gene names using the script `import_genes.pl` and we will provide the gene list file, species name, gene annotation version, and folder name for JBrowse (remember this name when you set up JBrowse). This way we can link the genes with the genome browser.

Open a terminal using docker-compose or Docker desktop

    docker-compose exec easy_gdb /bin/bash

use a Perl script to import the gene list:

    perl easy_gdb/scripts/import_genes.pl db_example_annotations/gene_list.txt "Homo sapiens" "1.0" "easy_gdb_sample"

It will ask for the host name (`DB`), DB name (`annot1`), and the postgres password.

Now we will add annotations to the genes using the script `import_annots_sch2.pl`. For that, we will need a file such as `annotation_example_SwissProt.txt`, where we have the first column with the gene name, the second column with the annotation term (ID for SwissProt, or a close related model species, GO term, InterProscan term, EC, KEGG, etc.), and a third column with the annotation description. As an example we will import annotations for SwissProt and TAIR10 (for model plant arabidopsis). The script needs the annotations file, name of the annotation (SwissProt, TAIR10, etc.), species name and annotation version.

Example for SwissProt annotations:

    perl easy_gdb/scripts/import_annots_sch2.pl db_example_annotations/annotation_example_SwissProt.txt SwissProt "Homo sapiens" "1.0"

Example for TAIR10 annotations:

    perl easy_gdb/scripts/import_annots_sch2.pl db_example_annotations/annotation_example_TAIR10.txt TAIR10 "Homo sapiens" "1.0"

You can add custom annotation links in the annotation_links.json file: `egdb_files/annotations/annotation_links.json`

``` json
{
  "TAIR10":"http://www.arabidopsis.org/servlets/TairObject?type=locus&name=query_id",
  "Araport11":"http://www.arabidopsis.org/servlets/TairObject?type=locus&name=query_id",
  "SwissProt":"http://www.uniprot.org/uniprot/query_id",
  "InterPro":"https://www.ebi.ac.uk/interpro/entry/InterPro/query_id",
  "NCBI":"https://www.ncbi.nlm.nih.gov/protein/query_id"
}
```

This file includes example links for TAIR10, Araport11, SwissProt, InterPro and NCBI. The name used (TAIR10, Araport11, SwissProt ...) should be used in the import_annots_sch2.pl script, as shown above. In the link, `query_id` will be replaced by the gene id or annotation term.



## Set up EasyGDB PostgreSQL database in Linux or servers

open the file `egdb_files/egdb_conf/database_access.php` and set up the database connection based on the user, password and database name you used. The host is `DB` for the Docker installation and `localhost` for linux installations in servers or personal computers:

``` php
function getConnectionString(){return "host=localhost dbname=annot1 user=web_usr password=password";};
```

Or in the case you will not use the relational database (for gene annotations):

``` php
function getConnectionString(){return null;};
```

## Set up password

If we installed PostgreSQL from scratch we need to create a password for postgres (it would be like the database default/root user).

You can use `\q` to exit the PostgreSQL console.

Connect to the database the first time:

``` bash
sudo -u postgres psql postgres
```

Create a password for the postgres user:

``` sql
\du
\password postgres
You will be asked to type your new password
\q
```

    psql -U postgres -h localhost -W

In Postgres console

Here, we will create a new database `annot1`. Any time you want to create a new database to test some data or new versions, you can create a new one and point to it in the file `egdb_files/egdb_conf/database_access.php`.

    CREATE DATABASE annot1;


    CREATE ROLE web_usr WITH LOGIN ENCRYPTED PASSWORD 'tmp_password' CREATEDB;
    \password web_usr

    \q

Back in your bash terminal

In this step we will create the database schema:

    psql -U postgres -d annot1 -h localhost -a -f easy_gdb/scripts/create_annot_schema2.sql

Here, we will learn how to import annotations to the database. First we will import all the gene names, for that we will need a file such as `easy_gdb/templates/annotations/gene_list.txt` with all the gene identifiers from our organism. It is recommended to use the transcript name (gene1.1).

We will import all the gene names using the script `import_genes.pl` and we will provide the gene list file, species name, gene annotation version, and folder name for JBrowse (remember this name to use it when you set up JBrowse). This way we can link the genes with the genome browser.

in (/var/www/html):

    perl easy_gdb/scripts/import_genes.pl db_example_annotations/gene_list.txt "Homo sapiens" "1.0" "easy_gdb_sample"

    perl easy_gdb/scripts/import_annots_sch2.pl db_example_annotations/annotation_example_SwissProt.txt SwissProt "Homo sapiens" "1.0"

    perl easy_gdb/scripts/import_annots_sch2.pl db_example_annotations/annotation_example_TAIR10.txt TAIR10 "Homo sapiens" "1.0"


## Set up server

In a server (not mandatory for local installations) you would need to use Apache or Nginx web servers to host your application in a server. For example, you could install apache:

``` bash
sudo apt-get install apache2

cd /etc/apache2/
sudo cp 000-default.conf easy_gdb.conf
sudo a2dissite 000-default.conf
sudo a2ensite easy_gdb.conf
systemctl reload apache2
```

For example, you can add the database directory as DocumentRoot to serve easy_gdb in your server (server_address/easy_gdb)

    DocumentRoot /var/www/html

or:

    DocumentRoot /home/user/example_db

Remember to change the paths in the configuration file.

In many cases, after applying some changes you will need to restart the server to make the changes effective:

``` bash
sudo service apache2 restart
```

