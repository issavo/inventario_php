<?php
    
    require_once "main.php";

    # Almacenando datos #
    $nombre = limpiar_cadena($_POST["usuario_nombre"]);
    $apellido = limpiar_cadena($_POST["usuario_apellido"]);

    $usuario = limpiar_cadena($_POST["usuario_usuario"]);
    $email = limpiar_cadena($_POST["usuario_email"]);

    $contrasenya_1 = limpiar_cadena($_POST["usuario_contrasenya_1"]);
    $contrasenya_2 = limpiar_cadena($_POST["usuario_contrasenya_2"]);


    # Verificacion de datos obligatorios #
    if ($nombre == " " || $apellido == " " || $usuario == " " || $contrasenya_1 == " " || $contrasenya_2 == " "){
        echo '
            <div class="notification is-danger">
                <strong> Ocurrio un error inesperado</strong><br/>
                No has rellenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    # Verificacion integridad de los datos #
    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
        echo '
            <div class="notification is-danger">
                <strong> Ocurrio un error inesperado</strong><br/>
                El NOMBRE no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
        echo '
            <div class="notification is-danger">
                <strong> Ocurrio un error inesperado</strong><br/>
                El APELLIDO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }
    if(verificar_datos("[a-zA-Z0-9]{4,20}", $usuario)){
        echo '
            <div class="notification is-danger">
                <strong> Ocurrio un error inesperado</strong><br/>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $contrasenya_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $contrasenya_1)){
        echo '
            <div class="notification is-danger">
                <strong> Ocurrio un error inesperado</strong><br/>
                Las CONTRASEÑAS no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    # Verificacion del email #
    if($email !== " "){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $check_email = conexion();
            $check_email = $check_email->query("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
            if($check_email->rowCount() > 0){
            echo '
                <div class="notification is-danger">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    El EMAIL introducido ya se ha registrado
                </div>
            ';
            exit();
            }
            $check_email = null;
        } else {
            echo '
                <div class="notification is-danger">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    El EMAIL introducido no es valido
                </div>
            ';
            exit();
        }
    }

    # Verificacion de usuario #
    $check_usuario = conexion();
    $check_usuario = $check_usuario->query("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
    if($check_usuario->rowCount() > 0){
        echo '
        <div class="notification is-danger">
            <strong> Ocurrio un error inesperado</strong><br/>
            El USUARIO introducido ya se ha registrado
        </div>
        ';
        exit();
    }
    $check_usuario = null;

    # Verificacion de contraseñas iguales #
    if($contrasenya_1 != $contrasenya_2){
    echo '
        <div class="notification is-danger">
            <strong> Ocurrio un error inesperado</strong><br/>
            Las CONTRASEÑAS no coinciden
        </div>
    ';
    exit();
    } else {
        $contrasenya = password_hash($contrasenya_1, PASSWORD_BCRYPT,["cost"=>10]);
    }

    # Guardando registros #
    $guardar_usuario=conexion();
    $guardar_usuario=$guardar_usuario->prepare("INSERT INTO usuario (
                                                    usuario_nombre,
                                                    usuario_apellido,
                                                    usuario_usuario,
                                                    usuario_clave,
                                                    usuario_email) 
                                                VALUES(
                                                        :nombre,
                                                        :apellido,
                                                        :usuario,
                                                        :contrasenya,
                                                        :email
                                                    )
                                                ");
    $marcadores=[
        ":nombre"=>$nombre,
        ":apellido"=>$apellido,
        ":usuario"=>$usuario,
        ":contrasenya"=>$contrasenya,
        ":email"=>$email
    ];
    $guardar_usuario->execute($marcadores);

    if($guardar_usuario->rowCount()==1){
        echo '
            <div class="notification is-info" is-light>
                <strong>Usuario registrado</strong><br/>
                El USUARIO ha sido registrado correctamente
            </div>
        ';
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                No se pudo registrar el USUARIO, por favor intentelo de nuevo
            </div>
        ';
    }
    $guardar_usuario=null;

?>