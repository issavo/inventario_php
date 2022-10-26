<?php

    require_once "../inc/session_start.php";
    require_once "main.php";

    // Almacenar id
    $id=limpiar_cadena($_POST['usuario_id']);

    // Verificacion de usuario
    $check_usuario=conexion();
    $check_usuario=$check_usuario->query("SELECT * FROM usuario WHERE usuario_id='$id'");

    if($check_usuario->rowCount()<=0){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                El usuario no existe en el sistema
            </div>
        ';
        exit();
    } else {
        $datos=$check_usuario->fetch();
    }
    $check_usuario=null;


    // Almacenar datos del administrador
    $admin_usuario=limpiar_cadena($_POST['administrador_usuario']);
    $admin_contrasenya=limpiar_cadena($_POST['administrador_contrasenya']);

    # Verificacion campos obligatorios del administrador #
    if ($admin_usuario == " " || $admin_contrasenya == " "){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                No has rellenado todos los campos que son obligatorios correspondientes al USUARIO y CONTRASEÑA
            </div>
        ';
        exit();
    }

    # Verificacion integridad de los datos #
    if(verificar_datos("[a-zA-Z0-9]{4,20}", $admin_usuario)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                Su USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $admin_contrasenya)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                Su CONTRASEÑA no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    // Verificacion del administrador en la BD
    $check_admin=conexion();
    $check_admin=$check_admin->query("SELECT usuario_usuario, usuario_clave FROM usuario WHERE usuario_usuario='$admin_usuario' AND usuario_id='".$_SESSION['id']."'");

    if ($check_admin->rowCount()==1) {
        $check_admin=$check_admin->fetch();

        if($check_admin['usuario_usuario'] != $admin_usuario || password_verify($admin_contrasenya, $check_admin['usuario_clave'])){
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    USUARIO o CONTRASEÑA del ADMINISTRADOR es incorreto 
                </div>
            ';
            exit();  
        }

    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                USUARIO o CONTRASEÑA del ADMINISTRADOR es incorreto 
            </div>
        ';
        exit();
    }
    $check_admin=null;

    # Almacenando datos del usuario#
    $nombre = limpiar_cadena($_POST["usuario_nombre"]);
    $apellido = limpiar_cadena($_POST["usuario_apellido"]);

    $usuario = limpiar_cadena($_POST["usuario_usuario"]);
    $email = limpiar_cadena($_POST["usuario_email"]);

    $contrasenya_1 = limpiar_cadena($_POST["usuario_contrasenya_1"]);
    $contrasenya_2 = limpiar_cadena($_POST["usuario_contrasenya_2"]);

     # Verificacion campos obligatorios del usuario#
    if ($nombre == " " || $apellido == " " || $usuario == " "){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                No has rellenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    # Verificacion integridad de los datos del usuario #
    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                El APELLIDO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9]{4,20}", $usuario)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    # Verificacion del email #
    if($email !== " " && $email != $datos['usuario_email']){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $check_email = conexion();
            $check_email = $check_email->query("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
            if($check_email->rowCount() > 0){
                echo '
                    <div class="notification is-danger is-light">
                        <strong> Ocurrio un error inesperado</strong><br/>
                        El EMAIL introducido ya se ha registrado
                    </div>
                ';
                exit();
            }
            $check_email=null;
        } else {
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    El EMAIL introducido no es valido
                </div>
            ';
            exit();
        }
    }

    # Verificacion de usuario #
    if($usuario != $datos['usuario_usuario']){
        $check_usuario = conexion();
        $check_usuario = $check_usuario->query("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
        if($check_usuario->rowCount() > 0){
            echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                El USUARIO introducido ya se ha registrado
            </div>
            ';
            exit();
        }
        $check_usuario = null;

    }

    # Verificacion de contraseñas iguales #
    if ($contrasenya_1 != " " || $contrasenya_2 != " ") {
        //verificacion de la integridad de los datos
        if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $contrasenya_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $contrasenya_1)){
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    Las CONTRASEÑAS no coincide con el formato solicitado
                </div>
            ';
            exit();
        } else {
            if ($contrasenya_1 != $contrasenya_2) {
                echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    Las CONTRASEÑAS no coinciden
                </div>
                ';
                exit();
            } else {
                $contrasenya = password_hash($contrasenya_1, PASSWORD_BCRYPT, ["cost" => 10]);
            }
        }
    } else {
        $contrasenya=$datos['usuario_clave'];
    }
   
    # Actualizar datos #
    $actualizar_usuario=conexion();
    $actualizar_usuario=$actualizar_usuario->prepare("UPDATE usuario SET            usuario_nombre=:nombre,usuario_apellido=:apellido,usuario_usuario=:usuario,usuario_clave=:contrasenya,usuario_email=:email WHERE usuario_id=:id");

    $marcadores=[
        ":nombre"=>$nombre,
        ":apellido"=>$apellido,
        ":usuario"=>$usuario,
        ":contrasenya"=>$contrasenya,
        ":email"=>$email,
        ":id"=>$id
    ];

    if($actualizar_usuario->execute($marcadores)){
        echo '
            <div class="notification is-info is-light">
                <strong> USUARIO actualizado</strong><br/>
                El USUARIO se actualizo correctamente
             </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    El USUARIO no se pudo actualizar, por favor intentelo de nuevo
                </div>
        ';
    }
    $actualizar_usuario=null;


?>