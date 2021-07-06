FROM php:7.4-apache

RUN apt-get update -y
RUN apt-get install -y libpq-dev
RUN apt-get install git -y


RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt-get install ncbi-blast+ -y
RUN apt-get install vim -y
RUN apt-get install less -y

RUN apt-get install cpanminus -y
RUN cpanm DBI
RUN cpanm Term::ReadKey
RUN cpanm DBD::Pg

# Needed for JBrowse
RUN cpanm local::lib
RUN apt-get install zlib1g-dev -y
RUN cpanm PerlIO::gzip

CMD ["apache2-foreground"]