<?php
    # Conexion a la base de datos #
    function conexion(){
        $pdo= new PDO("mysql:local=localhost;dbname=inventario", "root", "");
        return $pdo;
    }
    
    # verificar datos formulario #
    function verificar_datos($filtro,$cadena){
        if(preg_match("/^$".$filtro."/", $cadena)){
            return false;
        } else {
            return true;
        }
    }

   




   








?>