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

    # Directorio de imagenes #
    $img_dir="../img/producto/";

    # Comprobar si se selecciono una imagen #
    if ($_FILES['producto_foto']['name'] != " " && $_FILES['producto_foto']['size'] > 0) {

        # Crear directorio de imagen #
        if(!file_exists($img_dir)){
            if(!mkdir($img_dir,0777)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong> Ocurrió un error inesperado</strong><br/>
                        Error al crear el directorio
                    </div>
                ';
                exit();
            }
        }

        # Verificar formato imagen #
        if(mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/png"){
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrió un error inesperado</strong><br/>
                    La imagen seleccionada no tiene el formato permitido
                </div>
            ';
            exit();
        }

        # Verificacion tamaño de la imagen #
        if(($_FILES['producto_foto']['size']/1024) > 1024*3){
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrió un error inesperado</strong><br/>
                    La imagen seleccionada supera el peso permitido
                </div>
            ';
            exit();
        }

        # Extension de la imagen #
        switch (mime_content_type($_FILES['producto_foto']['tmp_name'])) {
            case 'img/jpeg':
                $img_ext=".jpeg";
                break;
            case 'img/png':
                $img_ext=".png";
                break;
        }

        chmod($img_dir,0777);

        $img_nombre=renombrar_fotos($nombre);
        $foto=$img_nombre.$img_ext;

        # Mover imagen al directorio #
        if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrió un error inesperado</strong><br/>
                    No se puede subir la imagen al sistema en este momento
                </div>
            ';
            exit();
        }
    } else {
        $foto="";
    }

    # Guardar datos en la BD #
    $guardar_producto=conexion();
    $guardar_producto= $guardar_producto->prepare("INSERT INTO producto (producto_codigo,producto_nombre,producto_precio,producto_stock,producto_foto,categoria_id,usuario_id) VALUES(:codigo,:nombre,:precio,:stock,:foto,:categoria,:usuario)");

    $marcadores=[
        ":codigo"=>$codigo,
        ":nombre"=>$nombre,
        ":precio"=>$precio,
        ":stock"=>$stock,
        ":foto"=>$foto,
        ":categoria"=>$categoria,
        ":usuario"=>$_SESSION['id']
    ];

    $guardar_producto->execute($marcadores);

    if($guardar_producto->rowCount()==1){
        echo '
            <div class="notification is-info" is-light>
                <strong>Producto registrado</strong><br/>
                El PRODUCTO ha sido registrado correctamente
            </div>
        ';
    } else {
        if(is_file($img_dir.$foto)){
            chmod($img_dir.$foto,0777);
            unlink($img_dir.$foto);
        }
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrió un error inesperado</strong><br/>
                No se pudo registrar el PRODUCTO, por favor inténtelo de nuevo
            </div>
        ';
    }
    $guardar_producto=null;
    












?>