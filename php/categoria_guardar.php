<?php
    require_once "main.php";

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

    # Guardar datos en la BD #
    $guardar_categoria=conexion();
    $guardar_categoria= $guardar_categoria->prepare("INSERT INTO categoria (categoria_nombre,categoria_ubicacion) VALUES(:nombre,:ubicacion)");

    $marcadores=[
        ":nombre"=>$nombre,
        ":ubicacion"=>$ubicacion
    ];
    
    $guardar_categoria->execute($marcadores);

    if($guardar_categoria->rowCount()==1){
        echo '
            <div class="notification is-info" is-light>
                <strong>Categoría registrado</strong><br/>
                El NOMBRE de la categoría ha sido registrado correctamente
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                No se pudo registrar el NOMBRE de la categoría, por favor inténtelo de nuevo
            </div>
        ';
    }
    $guardar_usuario=null;












?>