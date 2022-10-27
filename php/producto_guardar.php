<?php
    require_once "../inc/session_start.php";
    require_once "main.php";

     # Almacenando datos #
    $codigo = limpiar_cadena($_POST["producto_codigo"]);
    $nombre = limpiar_cadena($_POST["producto_nombre"]);
    $precio = limpiar_cadena($_POST["producto_precio"]);
    $stock = limpiar_cadena($_POST["producto_stock"]);
    $categoria = limpiar_cadena($_POST["producto_categoria"]);

     # Verificacion campos obligatorios #
    if ($codigo == " " || $nombre == " " ||  $precio == " " || $stock == " " || $categoria == " "){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                No has rellenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    # Verificacion integridad de los datos #
    if(verificar_datos("[a-zA-Z0-9- ]{1,70}", $codigo)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El CÓDIGO DE BARRAS no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}", $nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El NOMBRE DEL PRODUCTO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[0-9.]{1,25}", $precio)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El PRECIO DEL PRODUCTO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[0-9]{1,25}", $stock)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El STOCK DEL PRODUCTO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    # Verificacion codigo #
    $check_codigo = conexion();
    $check_codigo = $check_codigo->query("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
    if($check_codigo->rowCount() > 0){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El CÓDIGO introducido ya se ha registrado
            </div>
        ';
        exit();
    }
    $check_codigo = null;

    # Verificacion nombre #
    $check_nombre = conexion();
    $check_nombre = $check_nombre->query("SELECT producto_nombre FROM producto WHERE producto_nombre='$nombre'");
    if($check_nombre->rowCount() > 0){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El NOMBRE introducido ya se ha registrado
            </div>
        ';
        exit();
    }
    $check_nombre = null;

    # Verificacion nombre #
    $check_categoria = conexion();
    $check_categoria = $check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
    if($check_categoria->rowCount() > 0){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                La CATEGORÍA seleccionada no existe
            </div>
        ';
        exit();
    }
    $check_categoria = null;



    












?>