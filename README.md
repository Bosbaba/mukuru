Mukuru Practical Test

Notes and Assumptions:
======================
 * Since we normally know what environment we are deploying to I'm going to assume its a Debian-7/wheezy box for this exercise.
 * MySQL is installed on the local machine.
 * You have proficient knowledge of the Unix command line, as this installation must be performed from the command line.
 * Exchange rate max decimal is 8 for this exercise
 * Surcharge and discount percentages have a max of 2 decimals
 * I'll be storing currency with a max decimal of 2
 * I didn't subtract the discount from the stored surcharge amount, as the discount was applied on the final amount


I'm going to use the following technologies, packages and frameworks
 * nginx
 * php5-fpm
 * php5-cli
 * php5-curl
 * Laravel (php framework)
 * REST API
	
For size and simplicity, I'm going to use the same Laravel framework instance for both the frontend and the webservice,
they will normally be separate instances. I'll be accessing the webservice with a curl request, but in essence it will just
be calling itself. If you would like to put it on separate box you can just duplicate the code base AFTER configuration. Just use
the copy as the front end as the db will be configured as part of the initial configuration, which should run on the webservice side.

System Requirements:
====================
nginx
php5-fpm
php5-cli
php5-mysql
php5-mcrypt
mysql-server
php5-curl

Warning:
========
Preferably run the blow installation commands on a clean vm/machine if you're not proficient in unix systems, 
we don't want you to accidentally override your current system configurations

Installation:
=============
* Log into your wheeze box/vm
* Lets make sure everything is upgraded and up to date. Please run the following command in the command line.

		sudo apt-get update && sudo apt-get install     # You can add the -y option if you don't want to be prompted

* Install nginx if not yet installed by running the following command

		sudo apt-get install -y nginx    # This should not be installed along side apache as they might conflict on port 80

* Ok lets install the mysql server

        sudo apt-get install mysql-server   # You will be prompted for a root password(Make sure to remember this password as it will
                                            # be required for the db and db user creation)

* Installing all the php5 bits and bobs

		sudo apt-get install -y php5-fpm php5-cli php5-mysql php5-mcrypt php5-curl
		sudo service php5-fpm restart

* Lets make sure the default nginx webroot directory exists (You can change this if are using a different webroot)

        mkdir -p /var/www/mukuru.test

* Extract the project tar ball into this directory

        tar zxvf mukuru.test.tar.gz
        cd mukuru.test.tar.gz

* In the project directory lets update composer and vendor files, although I've already included them

        php composer.phar update

* Ok lets create the db and db user and seed the db (I've added a dump of the db structure and data to app/database/db_backups if required)
  # If you would like to use your own db and user, you can skip this step and configure the db credentials in app/config/database.php under the mysql section

        php artisan db:create

* NOTE: Only run these two commands if you've skipped the above step and are using a different db.

        php artisan migrate
        php artisan db:seed

* Make sure the web server has read permissions for this project
* Make sure the web server has write access to the following directory and its sub directories app/storage as it gets used for logs and sessions, etc
* Now for the configurations. I've configured most of them, but you might want to change some of the following configs.
    - Configure app/config/app.php
        * url   - if you want to use a different url
    - Configure app/config/api.php
        * url   - If you want to use a different url
    - Configure app/config/mail.php   - I've setup a temp smtp account for the mails sending, you can change this if required
    - Configure app/config/mukuru.php - The details in here will be used for sending the emails

The following steps are a bit out of scope I believe but use them if you like: (They should work if you use the pre-configured configs)

* I've included a nginx domain config file for mukuru.test(alias api.mukuru.test) that can be simlinked to nginx sites available

    ln -s /var/www/mukuru.test/etc/nginx/sites-available/mukuru.test /etc/nginx/sites-enabled/mukuru.test

* To bypass DNS setups, update the local host file i.e. /etc/hosts by adding the following line (This is so that the frontend can find the webservice)

    127.0.0.1 mukuru.test api.mukuru.test

* You will have to apply the above step to the machine you're going to view the test from, but just change the IP address to the IP of your test box
  (Depending on your OS, the location of the hosts file might differ)

* Ok lets restart nginx

    sudo service nginx restart

That should be it.... hope it al works

Update rates
===============
To update the rates from an external source https://www.oanda.com/ in this case run the following command from the application root i.e. /var/www/mukuru.test
    php artisan currency:update

Main files to look at:
======================
app/commands/*
app/controllers/*       exclude BaseController.php
app/database/migrations/*
app/database/seeds/*
app/models/*            exclude User.php
app/views/mukuru.php
app/views/emails/mukuru.php

Troubleshoot:
==============
You can turn laravel debugging on in
    app/config/app.php
Make sure php5-fpm is running, maybe restart it
    sudo service php5-fpm status
    sudo service php5-fpm restart
Make sure nginx is running
    sudo service nginx status           or      sudo nginx -t
    sudo service nginx restart
Check that the php curl and mcrypt modules are correctly configured and installed
Log directories have writer permissions i.e. app/storage
