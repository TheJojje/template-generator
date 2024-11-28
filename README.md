# template-generator
Template generator creates a easy way to have a consistent configuration on for example network devices like switches and firewalls.
It comes with user and group-management so that you can have multiple users/groups having their own templates.

## Installation of packages

Install the required packages
```puppet
sudo apt update
sudp apt upgrade
sudo apt install nginx mariadb-server mariadb-client git
```

Secure your mariadb-installation
```puppet
mysql_secure_installation
```

Create the database
```puppet
mysql -u root -p
MariaDB [(none)]> CREATE DATABASE template;
MariaDB [(none)]> CREATE USER 'template'@'localhost' IDENTIFIED BY 'password';
MariaDB [(none)]> GRANT ALL PRIVILEGES ON template.* TO 'template'@'localhost' WITH GRANT OPTION;
MariaDB [(none)]> FLUSH PRIVILEGES;
MariaDB [(none)]> exit
```

Download template-generator
```puppet
cd /var/www
git clone https://github.com/TheJojje/template-generator
```

Enter the catalog to import database
```puppet
cd template-generator
mysql -u username -p template < dump.sql
```

## Create the database


## Import the database
```puppet
mysql -u username -p template-generator < dump.sql
```

## Configure NGINX
