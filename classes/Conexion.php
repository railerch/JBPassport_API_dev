<?php

/*

@Conexion SQL Server ~ PHP

================================
Este archivo se encarga de conectar a la 
base de datos a traves de PDO
================================

 */

// Clase conexion

class Conexion
{
    public $success = NULL;
    public $error = NULL;
    public $info;

    public function __construct(string $ruta)
    {
        $this->info = $ruta;
    }

    public function conn_sqlsrv()
    {
        $temp       = file_get_contents($this->info);
        $datos      = json_decode($temp);
        $instance   = $datos[0]->instance;
        $server     = $datos[0]->server;
        $database   = $datos[0]->database;
        $user       = $datos[0]->user;
        $pass       = $datos[0]->pass;

        try {
            $dsn = "sqlsrv:server={$server};database={$database}";
            $dbh = new PDO($dsn, $user, $pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->success = true;
            return $dbh;
        } catch (PDOException $e) {
            $this->error = '[SQLSRV] HA OCURRIDO UN ERROR: <br>' . $e->getMessage();
        }
    }

    public function conn_sqlite()
    {
        try {
            $dsn = "sqlite:{$this->info}";
            $sqlite = new PDO($dsn);
            $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $sqlite;
            $this->success = true;
        } catch (PDOException $e) {
            $this->error = '[SQLITE] HA OCURRIDO UN ERROR: <br>' . $e->getMessage();
        }
    }

    public function __destruct()
    {
    }
}
