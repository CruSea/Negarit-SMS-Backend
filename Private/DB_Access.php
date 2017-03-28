<?php

class DB_Access{
    private $DB_NAME;
    private $DB_HOST;
    private $DB_USER;
    private $DB_PASS;
    private $conn;

    public function __construct(){
        $this->initDatabase();
    }

    private function initDatabase(){
        try{
            require_once "common.php";
            $this->DB_NAME = DB_NAME;
            $this->DB_HOST = DB_HOST;
            $this->DB_USER = DB_USER;
            $this->DB_PASS = DB_PASS;

            $DSN = "mysql:host={$this->DB_HOST};db_name={$this->DB_NAME};";
            $this->conn = new PDO($DSN, $this->DB_USER, $this->DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
            $this->conn->setAttribute(PDO::ATTR_PERSISTENT, true);
            if($this->conn === null)
            {
                die("Attempt to connect to the database was not successful");
            }
            
        }catch (Exception $e){
            die($e->getmessage());
        }
    }
    public function getConnection(){
        return $this->conn;
    }
}
?>