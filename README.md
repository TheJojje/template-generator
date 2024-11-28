# template-generator
Template generator creates a easy way to have a consistent configuration on for example network devices like switches and firewalls.
It comes with user and group-management so that you can have multiple users/groups having their own templates.

## Installation

Install the required packages
```puppet
sudo apt install nginx mariadb-server mariadb-client
```

Create the database
```puppet
mysql -u root -p
MariaDB [(none)]> CREATE DATABASE template-generator;
MariaDB [(none)]> CREATE USER 'template-generator'@'localhost' IDENTIFIED BY 'password';
MariaDB [(none)]> GRANT ALL PRIVILEGES ON template-generator.* TO 'template-generator'@'localhost' WITH GRANT OPTION;
MariaDB [(none)]> FLUSH PRIVILEGES;
```

Import the database
```puppet
mysql -u username -p template-generator < dump.sql
```
