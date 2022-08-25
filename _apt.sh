#sudo apt install nethogs

#sudo apt-get update
#sudo apt-get -y install network-manager  
#systemctl start NetworkManager.service
#systemctl enable NetworkManager.service


#sudo apt-get -y install tcpdump 
#sudo apt-get install -y yum
#sudo yum install -y openssl-devel

#sudo add-apt-repository universe
#sudo apt-get install build-essential libtool autoconf automake libssl-dev


#sudo apt-get -y install ldnsutils

#sudo apt-get install bind9utils

#rndc-confgen

#tail -f /var/log/messages
#rndc querylog
#rndc querylog

rndc-confgen -r /dev/urandom >  /etc/bind/rndc.conf

#/etc/bind/rndc.conf nor /etc/bind/rndc.key