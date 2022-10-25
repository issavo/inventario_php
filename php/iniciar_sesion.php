<?php

    # Almacenando datos #
    $usuario = limpiar_cadena($_POST["login_usuario"]);
    $contrasenya = limpiar_cadena($_POST["login_contrasenya"]);

    # Verificacion de datos obligatorios #
    if ($usuario == " " || $contrasenya == " "){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                No has rellenado todos los campos que son obligatorios
            </div>
        ';
        exit();
    }

    # Verificacion integridad de los datos #
    if(verificar_datos("[a-zA-Z0-9]{4,20}", $usuario)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                El USUARIO no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $contrasenya)){
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                La CONTRASEÑA no coincide con el formato solicitado
            </div>
        ';
        exit();
    }

    # Verificacion a la base de datos #
    $check_user = conexion();
    $check_user = $check_user->query("SELECT * FROM usuario WHERE usuario_usuario='$usuario'");

    if($check_user->rowCount()==1){
        $check_user= $check_user->fetch();

        if($check_user['usuario_usuario'] && password_verify($contrasenya,$check_user['usuario_clave'])){
            $_SESSION['id']=$check_user['usuario_id'];
            $_SESSION['nombre']=$check_user['usuario_nombre'];
            $_SESSION['apellido']=$check_user['usuario_apellido'];
            $_SESSION['usuario']=$check_user['usuario_usuario'];

            if(headers_sent()){
                echo "<script> window.location.href='index.php?vista=home'</script>";
            } else {
                header("Location:index.php?vista=home");
            }


        } else {
            echo '
                <div class="notification is-danger is-light">
                    <strong> Ocurrio un error inesperado</strong><br/>
                    El USUARIO o la CONTRASEÑA son incorrectos
                </div>
            ';
        }
    } else {
        echo '
            <div class="notification is-danger is-light">
                <strong> Ocurrio un error inesperado</strong><br/>
                El USUARIO o la CONTRASEÑA son incorrectos
            </div>
        ';
    }
    $check_user=null;
?>