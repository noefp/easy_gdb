FROM php:7.4-apache

RUN apt-get update -y
RUN apt-get install libpq-dev -y
RUN apt-get install git -y
RUN apt-get install zlib1g-dev -y
RUN apt-get install libexpat1-dev -y


RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt-get install ncbi-blast+ -y
RUN apt-get install vim -y
RUN apt-get install less -y
RUN apt-get install wget -y
RUN apt-get install zip -y

RUN apt-get install cpanminus -y
RUN cpanm DBI
RUN cpanm Term::ReadKey
RUN cpanm DBD::Pg

# Needed for JBrowse
RUN cpanm local::lib
RUN cpanm PerlIO::gzip
RUN apt-get install libhash-merge-perl libjson-xs-perl libjson-perl liblist-moreutils-perl libperlio-gzip-perl libparse-recdescent-perl liblocal-lib-perl libxml-parser-perl libbio-perl-perl -y
CMD ["apache2-foreground"]