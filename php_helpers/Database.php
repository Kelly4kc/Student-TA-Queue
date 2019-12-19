<?php

/**
 * Provides access to the underlying PostgreSQL database.
 */
class Database {

    public static function open() {
        $host = "webdev-group5.cyhnjzo4iwnw.us-east-1.rds.amazonaws.com";
        $port = "5432";
        $db = "taProj";
        $user= getenv("taProjDBUser");
        $pass = getenv("taProjDBpass");
        $con = pg_pconnect("host=$host port=$port dbname=$db user=$user password=$pass")
               or die("Could not connect: " . pg_last_error());
        return $con;
    }

}
