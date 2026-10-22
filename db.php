<?php  
    class db {
        private const host = "localhost";
        private const user = "root";
        private const password = "";
        private const database = "project337";

        public static function conn()
        {
            $conn = mysqli_connect(db::host, db::user, db::password, db::database);
            return $conn;
        }

        private function __construct()
        {
            throw new \Exception('Not implemented');
        }

    }
?>