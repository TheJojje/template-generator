# template-generator
Template generator creates a easy way to have a consistent configuration on for example network devices like switches and firewalls.
It comes with user and group-management so that you can have multiple users/groups having their own templates.

## Installation of packages

Install the required packages
```puppet
sudo apt update
sudp apt upgrade
sudo apt install nginx mariadb-server mariadb-client git php-fpm php-mysql
```

Secure your mariadb-installation
```puppet
mysql_secure_installation
```

## Database
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

## Configure NGINX
```puppet
server {
        listen 80;
        root /var/www/template-generator;

        index index.php
        server_name _;

        location / {
            try_files $uri $uri/ =404;
        }
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        }
}
```
Restart NGINX

## Access
You should now be able to login to the system via http://<IP>:80.

There are two default accounts<br>
admin/password (Site-Admin)<br>
user/password (Default tenant)

Enjoy!
