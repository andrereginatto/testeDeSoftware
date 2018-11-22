<?php


class DAO {
    static protected $db;
    
    function __construct() {
        if (!isset(DAO::$db)){
          DAO::$db = new PDO("mysql:host=localhost;dbname=controle","root","");
          DAO::$db->exec("SET CHARACTER SET utf8");
        }
    }
}
