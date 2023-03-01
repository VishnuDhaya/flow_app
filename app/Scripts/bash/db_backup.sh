#!/bin/bash
#!/bin/bash
#---------------------------------------------------------
# written by: lawrence mcdaniel
#             https://lawrencemcdaniel.com
#             https://blog.lawrencemcdaniel.com
#
# date:       feb-2018
#
# usage:      backup MySQL and MongoDB data stores
#             combine into a single tarball, store in "backups" folders in user directory
#
# reference:  https://github.com/edx/edx-documentation/blob/master/en_us/install_operations/source/platform_releases/ginkgo.rst
#---------------------------------------------------------

#------------------------------ SUPER IMPORTANT!!!!!!!! -- initialize these variables
#MYSQL_USER="admin"
#MYSQL_PWD="adm!n321#"      #Add your MySQL admin password, if one is set. Otherwise set to a null string

MYSQL_USER="flow_db_admin"
MYSQL_PWD="ev!l_m!nda_bd_w0!f"      #Add your MySQL admin password, if one is set. Otherwise set to a null string
MYSQL_HOST="flow-db-live-readonly-server.cppqgmkcxl5j.ap-south-1.rds.amazonaws.com"


S3_BUCKET="flow-db-backup-files"  #For this script to work you'll first need the following:
                                            # - create an AWS S3 Bucket
                                            # - create an AWS IAM user with programatic access and S3 Full Access privileges
                                            # - install AWS Command Line Tools in your Ubuntu EC2 instance
                                            # run aws configure to add your IAM key and secret token
#------------------------------------------------------------------------------------------------------------------------

PATH=$PATH:$HOME/bin:/home/anish/mysql2/bin
export PATH

BACKUPS_DIRECTORY="/usr/share/db_backups/"
WORKING_DIRECTORY="/usr/share/db_backups_tmp/"
NUMBER_OF_BACKUPS_TO_RETAIN="30"      #Note: this only regards local storage (ie on the ubuntu server). All backups are retained in the S3 bucket forever.

#Check to see if a working folder exists. if not, create it.
if [ ! -d ${WORKING_DIRECTORY} ]; then
    mkdir ${WORKING_DIRECTORY}
    echo "created backup working folder ${WORKING_DIRECTORY}"
fi

#Check to see if anything is currently in the working folder. if so, delete it all.
if [ -f "$WORKING_DIRECTORY/*" ]; then
  sudo rm -r "$WORKING_DIRECTORY/*"
fi

#Check to see if a backups/ folder exists. if not, create it.
if [ ! -d ${BACKUPS_DIRECTORY} ]; then
    mkdir ${BACKUPS_DIRECTORY}
    echo "created backups folder ${BACKUPS_DIRECTORY}"
fi


cd ${WORKING_DIRECTORY}

#Backup MySQL databases
#MYSQL_CONN="-u${MYSQL_USER} -p${MYSQL_PWD}"
MYSQL_CONN="-h ${MYSQL_HOST} -u${MYSQL_USER} -p${MYSQL_PWD}"
echo "Backing up MySQL databases"
echo "Reading MySQL database names..."
echo mysql ${MYSQL_CONN} -ANe "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT IN ('mysql','information_schema','performance_schema', 'sys')" > /tmp/db.txt

mysql ${MYSQL_CONN} -ANe "SELECT schema_name FROM information_schema.schemata WHERE schema_name NOT IN ('mysql','information_schema','performance_schema', 'sys')" > /tmp/db.txt

DBS="--databases $(cat /tmp/db.txt)"
NOW="$(date +%Y-%m-%dT%H%M%S)"
SQL_FILE="mysql-data-${NOW}.sql"
echo "Dumping MySQL structures..."
mysqldump ${MYSQL_CONN} --add-drop-database --no-data ${DBS} > ${SQL_FILE}
echo "Dumping MySQL data..."
# If there is table data you don't need, add --ignore-table=tablename
mysqldump ${MYSQL_CONN} --no-create-info ${DBS} >> ${SQL_FILE}
echo "Done backing up MySQL"


#Tarball all of our backup files
echo "Compressing backups into a single tarball archive"
tar -czf ${BACKUPS_DIRECTORY}flow-data-${NOW}.tgz ${SQL_FILE}
sudo chown root ${BACKUPS_DIRECTORY}flow-data-${NOW}.tgz
sudo chgrp root ${BACKUPS_DIRECTORY}flow-data-${NOW}.tgz
echo "Created tarball of backup data flow-data-${NOW}.tgz"

#FILE_SIZE=stat -c %s  ${BACKUPS_DIRECTORY}flow-data-${NOW}.tgz

JUNK_DIRECTORY=${BACKUPS_DIRECTORY}/junk

#Prune the Backups/ folder by eliminating all but the 30 most recent tarball files
echo "Pruning the local backup folder archive"
if [ -d ${BACKUPS_DIRECTORY} ]; then
  cd ${BACKUPS_DIRECTORY}

  ls -1tr | head -n -${NUMBER_OF_BACKUPS_TO_RETAIN} | xargs -d '\n' rm -f --
fi

#Remove the working folder
echo "Cleaning up"
sudo rm -r ${WORKING_DIRECTORY}

echo "Sync backup to AWS S3 backup folder"
aws s3 sync ${BACKUPS_DIRECTORY} s3://${S3_BUCKET}
echo "Done!"