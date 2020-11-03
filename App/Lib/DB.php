<?php

namespace App\Lib;

use PDO;
use PDOException;

class DB
{
    public $host;
    public $database;
    public $username;
    public $password;

    /**
     * DB constructor.
     */
    public function __construct()
    {
        $this->host = Config::get('DB_HOST');
        $this->database = Config::get('DB_DATABASE');
        $this->username = Config::get('DB_USERNAME');
        $this->password = Config::get('DB_PASSWORD');
    }

    /**
     * @return array|PDO
     */
    public function connection()
    {
        try {
            $conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->database . ";charset=utf8", $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            return ["error" => 1, "message" => "Veritabanı bağlantı hatası!"];
            exit;
        }
    }

    /**
     * @param $table
     * @param string $columns
     * @return array
     */
    public function select($table, $columns = "*")
    {
        $con = $this->connection();

        if(!is_object($con) && isset($con["error"])){
            return $con;
            exit;
        }

        if (is_array($columns)) {
            $columnString = "";
            foreach ($columns as $column) {
                $columnString .= " `" . $column . "`, ";
            }
            $columns = rtrim($columnString, ", ");
        }
        try {

            $sqlString = "select " . $columns . " from `" . $table . "`";
            $query = $con->query($sqlString, PDO::FETCH_OBJ);
            if ($query->rowCount()) {
                return $query->fetchAll();
            }

        } catch (PDOException $e) {
            return ["error" => 1, "message" => "Bir sorun oluştu!"];
        }
    }

    /**
     * @param $table
     * @param string $columns
     * @param $product_id
     * @return array|mixed|null
     */
    public function first($table, $columns = "*", $product_id)
    {
        $con = $this->connection();

        if(!is_object($con) && isset($con["error"])){
            return $con;
            exit;
        }

        if (is_array($columns)) {
            $columnString = "";
            foreach ($columns as $key => $column) {
                $columnString .= " `" . $column . "`, ";
            }
            $columnString = rtrim($columnString, ", ");
        }

        try {
            $sqlString = "select " . $columnString . " from `" . $table . "` where product_id = " . $product_id;
            $query = $con->query($sqlString, PDO::FETCH_OBJ);
            if ($query->rowCount()) {
                return $query->fetch();
            } else {
                return null;
            }

        } catch (PDOException $e) {
            return ["error" => 1, "message" => "Bir sorun oluştu!"];
        }
    }

    /**
     * @param $table
     * @param $columns
     * @return array|mixed|null
     */
    public function create($table, $columns)
    {
        $con = $this->connection();

        if(!is_object($con) && isset($con["error"])){
            return $con;
            exit;
        }

        if (is_array($columns)) {
            $columnString = "";
            $values = [];
            $col = [];
            foreach ($columns as $column => $value) {
                $columnString .= " " . $column . " = ?, ";
                array_push($values, $value);
                array_push($col, $column);
            }
            $columnString = rtrim($columnString, ", ");
        }

        $query = $con->prepare("INSERT INTO " . $table . " SET " . $columnString);
        $insert = $query->execute($values);
        if ($insert) {
            $last_id = $con->lastInsertId();
            $data = $this->first($table, $col, $last_id);

            return $data;
        }
    }
}