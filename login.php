<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>    
</body>

</html>
<?php

function accederAdmin($u, $c){
    if(isset($u) && isset($c)){
        if($u == 'Administrador' && $c == 'Administrador'){
            session_start(); // Iniciamos sesión cuando el usuario y la clave del admin son correctos
            $_SESSION['usuario'] = $u;
            $_SESSION['clave'] = $c;            
            return true;
        }else{
            $_SESSION['error'] = '<br><p style="color:red;">Usuario o contraseña incorrectos</p>'; // Vamos a crear una nueva variable en la sesión, que será un error al inciar sesion
           return false;
        }
    }else{
        return false;
    }
} // Con esta función, iniciamos sesión como administrador. Si no son correctos usuario y clave devolvemos false

function accederUsuario($u, $c){
    if(isset($u) && isset($c)){
        // Ahora almaceno en un array los usuarios y en otro las contraseñas desde el archivo usuarios.txt
        if(file_exists('usuarios.txt')){
            $archivoUsuarios = fopen("usuarios.txt", "r"); // abrimos el archivo en modo lectura
            $usuarios = array();
            $claves = array();
            while (($linea = fgets($archivoUsuarios)) !== false) { // mientras no lleguemos al final del archivo
                $datos = explode(":", trim($linea)); // con explode separamos los datos de la línea en un array 
                if (count($datos) == 2) { // aseguramos que la línea tenga el formato correcto               
                $usuarios[] = $datos[0];
                $claves[] = $datos[1];
                }
            }  
        }      

        // ahora si $u está presente en el array usuarios y $c en el array claves, iniciamos sesión
        if(in_array($u, $usuarios) && in_array($c, $claves)){
            session_start(); // Iniciamos sesión cuando el usuario y la clave del usuario son correctos
            $_SESSION['usuario'] = $u;
            $_SESSION['clave'] = $c;            
            return true;        
        }else{
            $_SESSION['error'] = '<br><p style="color:red;">Usuario o contraseña incorrectos</p>'; // Si no están en el array crearemos el error
           return false;
        }
    }else{
        return false;
    }
} // Con esta función, iniciamos sesión como usuario. Si no son correctos usuario y clave devolvemos false

if(accederAdmin($_POST['usuario'], $_POST['clave'])){
    header('Location: mainAdmin.php'); // Si el usuario y la clave son correctos, nos redirige a la página principal del administrador
}else{
    echo '<br><div class="contenedor">
          <h1 style="color:red; text-align:center; -webkit-text-stroke: 1px #000000;">Usuario o contraseña incorrectos<br><br>Game Over</p>
          </div>';
    header('refresh: 1.5;url=index.html');
    // Si no son correctos, nos muestra un mensaje de error y al cabo de 1.5 segundo nos redirige a la página de inicio de sesión
}

if(accederUsuario($_POST['usuario'], $_POST['clave'])){
    header('Location: mainCliente.php'); // Si el usuario y la clave son correctos, nos redirige a la página principal del cliente
} // Si no son correctos, nos mostrará el mensaje de error 

if(isset($_POST['action']) && $_POST['action'] == 'Cerrar Sesion'){    
    session_destroy(); // Si pulsamos el botón de cerrar sesión, destruimos la sesión y nos redirige a la página de inicio de sesión
    header('Location: index.html');
}


?>