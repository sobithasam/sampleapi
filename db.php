<?php
class Db

{

    private static $db; 
    public static function getDb()
    {
        if (!self::$db)
        {
            try {
                $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
                // establish a connection

                self::$db = new PDO($dsn, DB_USER, DB_PWD);

                // after each error, throw exceptions

                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // set default fetch mode

                self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            } catch (PDOException $e) {

                die('Connection error: ' . $e->getMessage());

            }

        }

        return self::$db;

    }

}
?>
