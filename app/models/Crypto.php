<?php

class Crypto
{
    public $id;
    public $precio;
    public $nombre;
    public $foto;
    public $nacionalidad;

    public function crearCrypto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO cryptos (nombre, precio, nacionalidad, foto) VALUES (:nombre, :precio, :nacionalidad, :foto)");
        $consulta->bindValue(':nombre', $this->nombre);
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad);
        $consulta->bindValue(':foto', $this->foto);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, nacionalidad, foto FROM cryptos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Crypto');
    }

    public static function obtenerCrypto($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, nacionalidad, foto FROM cryptos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Crypto');
    }

    public function modificarCrypto()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE cryptos SET nombre = :nombre, precio = :precio, nacionalidad = :nacionalidad, foto = :foto WHERE id = :id");
        $consulta->bindValue(':nombre', $this->nombre);
        $consulta->bindValue(':precio', $this->precio);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad);
        $consulta->bindValue(':foto', $this->foto);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarCrypto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM cryptos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerPorNacion($nacion){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, nacionalidad, foto FROM cryptos WHERE nacionalidad = :nacion");
        $consulta->bindValue(':nacion', $nacion);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Crypto');
    }
}