
echo -e "\x1B[31m+++++++++++++++++++++++++++++++++++++++++++++++++++\x1B[0m"
echo -e "\x1B[31m      _____   \x1B[0m                    __________  ____ "
echo -e "\x1B[31m\___/  ___ \  \x1B[0m___  _______  __   / ____/ __ \/ __ )"
echo -e "\x1B[31m \_________/\x1B[0m/ __ \/ ___/ / / /  / / __/ / / / __  |"
echo -e "\x1B[31m   /  /____\x1B[0m/ /_/ (__  ) /_/ /  / /_/ / /_/ / /_/ / "
echo -e "\x1B[31m   \______/\x1B[0m\___,_\___/\__, /   \____/_____/_____/  "
echo -e "\x1B[31m           \x1B[0m          /____/                        "
echo -e "\x1B[31m+++++++++++++++++++++++++++++++++++++++++++++++++++\x1B[0m"

cp -r ../templates/* ../../;
ln -s -f /var/www/html/apache/easy_gdb_apache.conf /etc/apache2/sites-enabled/000-default.conf;
cd ../../

echo -e "\033[31mDo you want to install JBrowse 1?\033[0m (Y/n)"
read -p "> " option
option=${option:-Y}

while [[ $option != 'Y' && $option != 'y' && $option != 'N' && $option != 'n' ]]; do
    echo -e "\033[31mDo you want to install JBrowse 1?\033[0m (Y/n)"
    read -p "> "  option
    option=${option:-Y}
done

case "$option" in
    [Yy])
        wget https://github.com/GMOD/jbrowse/releases/download/1.16.11-release/JBrowse-1.16.11.zip
        unzip JBrowse-1.16.11.zip
        mv JBrowse-1.16.11/ jbrowse/
        cd jbrowse/
        ./setup.sh
        bin/prepare-refseqs.pl --fasta ../jbrowse_example_data/genome.fasta --out data/easy_gdb_sample
        bin/flatfile-to-json.pl -gff ../jbrowse_example_data/gene_models.gff --key "EasyGDB gene models" --trackLabel egdb_gene_models --trackType CanvasFeatures --type mRNA --out data/easy_gdb_sample
        bin/generate-names.pl --tracks egdb_gene_models --out data/easy_gdb_sample/
        cp ../jbrowse_example_data/jbrowse.conf .
        cp ../jbrowse_example_data/tracks.conf data/easy_gdb_sample/
        cp ../jbrowse_example_data/trackList.json data/easy_gdb_sample/
        cd ../
        ;;
    [Nn])
        echo "JBrowse will not be installed"
        ;;
esac

# Work in progress
# read -p "Do you want to download JBrowse 2? (Y/n) " option
# option=${option:-Y}

# while [[ $option != 'Y' && $option != 'n' ]]; do
#     read -p "Do you want to download JBrowse 2? (Y/n) " option
#     option=${option:-Y}
# done

# if [ "$option" == "Y" ]; then
# apt-get install tabix;
# curl -fsSL https://deb.nodesource.com/setup_22.x -o nodesource_setup.sh
# bash nodesource_setup.sh
# apt-get install nodejs
# cd ../;

# npm install -g @jbrowse/cli;
# jbrowse create jbrowse2;
# mkdir -p jbrowse2/data;
# fi

