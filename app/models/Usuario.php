<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $isAdmin = false;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, isAdmin) VALUES (:usuario, :clave, :isAdmin)");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT, array('cost'=> 10));
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':isAdmin', $this->isAdmin, PDO::PARAM_BOOL);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, isAdmin FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, isAdmin FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, rol = :rol WHERE id = :id");
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public static function obtenerPorId($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, isAdmin FROM usuarios WHERE id = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }


    public static function ObtenerPorCrypto($crypto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT DISTINCT u.id, u.usuario 
            FROM usuarios as u INNER JOIN ventas ON u.id = ventas.id_usuario
            INNER JOIN cryptos ON cryptos.id = ventas.id_crypto
            WHERE cryptos.nombre = :crypto");
        $consulta->bindValue(':crypto', $crypto);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }
}