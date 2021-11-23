<?php

class Venta
{
    public $id;
    public $fecha;
    public $cantidad;
    public $imagen;
    public $id_usuario;
    public $id_crypto;

    public function crearVenta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventas (fecha, cantidad, imagen, id_usuario, id_crypto) 
        VALUES (:fecha, :cantidad, :imagen, :id_usuario, :id_crypto)");
        
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':imagen', $this->imagen);
        $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(':id_crypto', $this->id_crypto, PDO::PARAM_INT);
        
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fecha, cantidad, imagen, id_usuario, id_crypto FROM ventas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }

    public static function obtenerVenta($venta)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, fecha, cantidad, imagen, id_usuario, id_crypto FROM ventas WHERE id = :id");
        $consulta->bindValue(':id', $venta, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Venta');
    }

    public function modificarVenta()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventas  SET cantidad = :cantidad, id_usuario = :usuario, id_crypto = :crypto WHERE id = :id");
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $this->id_usuario, PDO::PARAM_INT);
        $consulta->bindValue(':crypto', $this->id_crypto, PDO::PARAM_INT);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarVenta($venta)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM ventas WHERE id = :id");
        $consulta->bindValue(':id', $venta, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function obtenerPorNacion($nacion){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT v.id, v.fecha, v.cantidad, v.imagen, v.id_usuario, v.id_crypto 
            FROM ventas as v INNER JOIN cryptos ON v.id_crypto = cryptos.id 
            WHERE cryptos.nacionalidad = :nacion and 
            v.fecha between '2021-6-10' and '2021-6-13'");
        $consulta->bindValue(':nacion', $nacion);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }
}