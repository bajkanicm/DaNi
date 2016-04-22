<?php
        //ENTER THE RELEVANT INFO BELOW
        $mysqlDatabaseName ='dadanina';
        $mysqlUserName ='dadanina';
        $mysqlPassword ='Parfemi2014';
        $mysqlHostName ='localhost';
        $mysqlExportPath ='database18042015.sql';

        //DO NOT EDIT BELOW THIS LINE
        //Export the database and output the status to the page
        $command='mysqldump --opt -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' > ' .$mysqlExportPath;
        exec($command);
 ?>