<?php
    require_once "main.php";

    // Almacenar id categoria
    $id=limpiar_cadena($_POST['categoria_id']);

    // Verificacion de usuario
    $check_categoria=conexion();
    $check_categoria= $check_categoria->query("SELECT * FROM categoria WHERE categoria_id='$id'");

    if($check_categoria->rowCount()<=0){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                La CATEGORÍA no existe en el sistema
            </div>
        ';
        exit();
    } else {
        $datos= $check_categoria->fetch();
    }
    $check_categoria=null;

    # Almacenando datos de categoria #
    $nombre = limpiar_cadena($_POST["categoria_nombre"]);
    $ubicacion = limpiar_cadena($_POST["categoria_ubicacion"]);

    # Verificacion campos obligatorios #
    if ($nombre == " "){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                No has rellenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

     # Verificacion integridad de los datos #
    if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if($ubicacion != ""){
        if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}", $ubicacion)){
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrió un error inesperado</strong><br/>
                    La UBICACIÓN no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    }

    # Verificacion nombre de la categoria en la BD #
    if($nombre!=$datos['categoria_nombre']){
        $check_nombre = conexion();
        $check_nombre = $check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
        if($check_nombre->rowCount() > 0){
            echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                El NOMBRE introducido ya está registrado, por favor inténtelo de nuevo con otro
            </div>
            ';
            exit();
        }
        $check_nombre = null;
    }

    # Actualizar datos #
    $actualizar_categoria=conexion();
    $actualizar_categoria= $actualizar_categoria->prepare("UPDATE categoria SET     categoria_nombre=:nombre,categoria_ubicacion=:ubicacion WHERE categoria_id=:id");

    $marcadores=[
        ":nombre"=>$nombre,
        ":ubicacion"=>$ubicacion,
        ":id"=>$id
    ];

    if($actualizar_categoria->execute($marcadores)){
        echo '
            <div class="notification is-info is-light">
                <strong> CATEGORÍA actualizada</strong><br/>
                La CATEGORÍA ha sido actualizada correctamente
             </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                    <strong> Ocurrió un error inesperado</strong><br/>
                    La CATEGORÍA no se pudo actualizar, por favor inténtelo de nuevo
                </div>
        ';
    }
    $actualizar_categoria=null;



?>