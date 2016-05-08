#!/bin/bash
DBNAME="mukuru_db"
DBUSER="mukuru_usr"
DBPSWRD="!m#k#r#-zyx!" 
DBQUERY="create database $DBNAME;GRANT CREATE, DROP, DELETE, INSERT, SELECT, UPDATE, ALTER, INDEX ON $DBNAME.* TO $DBUSER@localhost IDENTIFIED BY '$DBPSWRD';FLUSH PRIVILEGES;"

echo  "This script will be creating the MySQL db and user:"
echo  "NOTE: The password you are promted for is your mysql root password"
mysql -u root -p -e "$DBQUERY"
 
if [ $? != "0" ]; then
 echo "[Error]: Database creation failed"
 exit 1
else
 echo "------------------------------------------"
 echo " Database has been created successfully "
 echo "------------------------------------------"
 echo " DB Info: "
 echo ""
 echo " DB Name: $DBNAME"
 echo " DB User: $DBUSER"
 echo " DB Pass: $DBPSWRD"
 echo ""
 echo "------------------------------------------"
fi
