<?php
/**
 * ============================================================
 *   CONFIGURACIÓN DE CONEXIÓN A BASE DE DATOS (PDO)
 * ------------------------------------------------------------
 *  Este archivo maneja la conexión principal del sistema.
 *  Utiliza PDO para mayor seguridad y soporte moderno.
 * 
 *  ✔ Compatible con MySQL / MariaDB
 *  ✔ Manejo de excepciones
 *  ✔ Charset seguro UTF8MB4
 *  ✔ Clase lista para ser usada en cualquier archivo
 * ============================================================
 */

class Database
{
    private $host = "localhost";     // Servidor de la base de datos
    private $dbname = "sistema_auth"; // Nombre de la BD
    private $username = "root";      // Usuario de BD
    private $password = "";          // Contraseña de BD
    
    private $conn; // Guarda la conexión PDO

    /**
     * ------------------------------------------------------------
     *  Método principal que retorna una conexión PDO segura.
     * ------------------------------------------------------------
     */
    public function getConnection()
    {
        $this->conn = null;

        try {

            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

            $this->conn = new PDO($dsn, $this->username, $this->password, [

                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Errores como excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                 // Mejor seguridad en consultas

            ]);

        } catch (PDOException $e) {

            // Mensaje claro si la BD falla
            die(" ERROR DE CONEXIÓN A LA BASE DE DATOS: " . $e->getMessage());
        }

        return $this->conn;
    }
}


