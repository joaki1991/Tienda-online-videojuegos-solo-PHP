<?php
session_start(); // Iniciamos la sesión

// Función para eliminar una línea de un archivo
function eliminarLinea($archivo, $lineaEliminar) {
    $archivoOriginal = fopen($archivo, "r");
    $archivoTemporal = fopen("temp.txt", "w");

    while (($linea = fgets($archivoOriginal)) !== false) {
        if (trim($linea) != $lineaEliminar) {
            fwrite($archivoTemporal, $linea);
        }
    }

    fclose($archivoOriginal);
    fclose($archivoTemporal);

    // Reemplazar el archivo original con el archivo temporal
    rename("temp.txt", $archivo);
}

// Si pulsamos el botón X, eliminamos el juego y la consola
if (isset($_POST['action']) && $_POST['action'] == "X") {
    eliminarLinea("juegos.txt", $_POST['juegoEliminar'] . ":" . $_POST['consolaEliminar']); // llamamos a la función eliminarLinea
    if (file_exists("alquileres.txt")) { // si existe el archivo alquileres.txt
       // si existe el usuario en alquileres.txt, eliminamos la línea, para ello voy a leer el archivo y crear una array con usuario y juego
        $archivoAlquileres = fopen("alquileres.txt", "r"); // abrimos el archivo en modo lectura
        $alquileres = array(); // inicializamos el array
        while (($lineaAlquiler = fgets($archivoAlquileres)) !== false) { // mientras no lleguemos al final del archivo
            $datosAlquiler = explode(":", trim($lineaAlquiler)); // con explode separamos los datos de la línea en un array
            if (count($datosAlquiler) == 2) { // aseguramos que la línea tenga el formato correcto
                $alquileres[] = $datosAlquiler; // guardamos los datos en el array
            }
        }
        fclose($archivoAlquileres); // cerramos el archivo
        // ahora voy a realizar un bucle for each para recorrer el array y eliminar las líneas que contengan el juego
        foreach ($alquileres as $alquiler) {
            if ($alquiler[1] == $_POST['juegoEliminar']) { // si el juego coincide
                eliminarLinea("alquileres.txt", $alquiler[0] . ":" . $alquiler[1]); // llamamos a la función eliminarLinea
            }
        }
    }
    header('Location: mainAdmin.php'); // refrescamos la página para que se muestre el juego eliminado
    exit(); // Asegura que el script se detenga después de la redirección
}

// Si pulsamos el botón de eliminar, eliminamos el usuario y contraseña
if (isset($_POST['action']) && $_POST['action'] == "Eliminar") {
    eliminarLinea("usuarios.txt", $_POST['usuarioEliminar'] . ":" . $_POST['claveEliminar']); // llamamos a la función eliminarLinea
    if (file_exists("alquileres.txt")) { // si existe el archivo alquileres.txt
        // si existe el usuario en alquileres.txt, eliminamos la línea, para ello voy a leer el archivo y crear una array con usuario y juego
        $archivoAlquileres = fopen("alquileres.txt", "r"); // abrimos el archivo en modo lectura
        $alquileres = array(); // inicializamos el array
        while (($lineaAlquiler = fgets($archivoAlquileres)) !== false) { // mientras no lleguemos al final del archivo
            $datosAlquiler = explode(":", trim($lineaAlquiler)); // con explode separamos los datos de la línea en un array
            if (count($datosAlquiler) == 2) { // aseguramos que la línea tenga el formato correcto
                $alquileres[] = $datosAlquiler; // guardamos los datos en el array
            }
        }
        fclose($archivoAlquileres); // cerramos el archivo
        // ahora voy a realizar un bucle for each para recorrer el array y eliminar las líneas que contengan el usuario
        foreach ($alquileres as $alquiler) {
            if ($alquiler[0] == $_POST['usuarioEliminar']) { // si el usuario coincide
                eliminarLinea("alquileres.txt", $alquiler[0] . ":" . $alquiler[1]); // llamamos a la función eliminarLinea
            }
        }        
    }
    header('Location: mainAdmin.php'); // refrescamos la página para que se muestre el usuario eliminado
    exit(); // Asegura que el script se detenga después de la redirección
}

// Si se pulsa el botón de añadir juego
if (isset($_POST['action']) && $_POST['action'] == "Añadir Juego") {
    if (empty($_POST['nuevoJuego']) || empty($_POST['consola'])) {        
    } else {
        // si los datos no están vacíos, los guardo en el archivo juegos.txt
        $nuevoJuego = $_POST['nuevoJuego'];
        // las consolas las voy a transformar en un string separado por comas
        $consolas = implode(",", $_POST['consola']);                        
        $archivoJuegos = fopen("juegos.txt", "a"); // abrir en modo append para no sobrescribir
        fwrite($archivoJuegos, $nuevoJuego . ":" . $consolas . "\n"); // Con fwrite escribimos en el archivo                        
        fclose($archivoJuegos); // por último cerramos el archivo        
        header('refresh: 1;url=mainAdmin.php'); // refrescamos la página para que se muestre el nuevo juego
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1 id="titulo">Administrador Web</h1>
    <?php
    echo "<div class='contenedor'>
          <p>Bienvenido " . $_SESSION['usuario'] . "</p>
          <p>Aquí podrás gestionar tu tienda de alquiler de videojuegos</p>
          </div>"; // Mostramos el nombre de administrador y una introducción a la página
    ?>
    <div class="contenedorExterno">
        <div class="contenedor">
            <h2 id="subtitulo">Registro de usuarios:</h2>
            <?php
            // Aquí mostraré los usuarios y contraseñas creados
            // Ahora voy a leer el archivo usuarios.txt y mostraré los usuarios y contraseñas
            // Para esto primero compruebo si el archivo existe
            if (file_exists("usuarios.txt")) {
                $archivoUsuarios = fopen("usuarios.txt", "r"); // abrimos el archivo en modo lectura
                echo "<table style='display:flex; justify-content: center;'>
                      <tr><td style='padding:10px'><u>Usuario</u></td><td style='padding:10px'><u>Contraseña</u></td></tr>";
                while (($linea = fgets($archivoUsuarios)) !== false) { // mientras no lleguemos al final del archivo
                    $datos = explode(":", trim($linea)); // con explode separamos los datos de la línea en un array
                    if (count($datos) == 2) { // aseguramos que la línea tenga el formato correcto
                        echo "<form action='mainAdmin.php' method='post'>"; // creamos un formulario
                        echo "<input type='hidden' name='usuarioEliminar' value='" . $datos[0] . "'>"; // guardamos el usuario en un input hidden
                        echo "<input type='hidden' name='claveEliminar' value='" . $datos[1] . "'>"; // guardamos la clave en un input hidden
                        echo "<tr><td style='padding:10px'>" . $datos[0] . "</td><td style='padding:10px'>" . $datos[1] . "</td>
                              <td><input type='submit' name='action' value='Eliminar' class='eliminar'></td></tr></form>"; // mostramos los datos
                    }
                }
                echo "</table>";
                fclose($archivoUsuarios); // cerramos el archivo
            } else {
                echo "<p>No hay usuarios registrados</p>"; // si no existe el archivo, mostramos un mensaje
            }
            ?>
        </div>
        <div class="contenedor2">
            <form action="mainAdmin.php" method="post">
                <h2 id="subtitulo">Añadir Usuarios</h2>
                <label>Nuevo Usuario</label>
                <input type="text" name="nuevoUsuario"><br><br>
                <label>Nueva Contraseña</label>
                <input type="password" name="nuevaClave"><br><br><br>
                <input type="submit" name="action" value="Añadir Usuario" class="botones">
                <?php
                // Voy a mostrar un mensaje en caso de que los datos introducidos estén vacíos
                if (isset($_POST['action']) && $_POST['action'] == "Añadir Usuario") {
                    if (empty($_POST['nuevoUsuario']) || empty($_POST['nuevaClave'])) {
                        echo "<p style='color:red;'>Debes introducir un usuario y una contraseña</p>";
                    } else {
                        // si los datos no están vacíos, los guardo en el archivo usuarios.txt
                        $nuevoUsuario = $_POST['nuevoUsuario'];
                        $nuevaClave = $_POST['nuevaClave'];
                        $archivoUsuarios = fopen("usuarios.txt", "a"); // abrir en modo append para no sobrescribir
                        fwrite($archivoUsuarios, $nuevoUsuario . ":" . $nuevaClave . "\n"); // Con fwrite escribimos en el archivo
                        fclose($archivoUsuarios); // por último cerramos el archivo
                        echo "<p style='color:green;'>Usuario añadido correctamente</p>";
                        header('refresh: 1;url=mainAdmin.php'); // refrescamos la página para que se muestre el nuevo usuario
                    }
                }
                ?>
            </form>
        </div>
    </div>
    <div class="contenedorExterno">
        <div class="contenedor">
            <h2 id="subtitulo">Resgistro de Videojuegos:</h2>
            <?php
            // Aquí mostraré los juegos introducidos y si estos están alquilados por algún usuario o no (-)
            // Ahora voy a leer el archivo juegos.txt y mostraré los juegos y consolas
            // Para esto primero compruebo si el archivo existe
            if (file_exists("juegos.txt")) {
                $archivoJuegos = fopen("juegos.txt", "r"); // abrimos el archivo en modo lectura
                echo "<table style='display:flex; justify-content: center;'>
                      <tr><td style='padding:10px'><u>Juego</u></td><td style='padding:10px'><u>Videoconsola</u></td><td style='padding:10px'><u>Alquilado por</u></td></tr>";
                while (($linea = fgets($archivoJuegos)) !== false) { // mientras no lleguemos al final del archivo
                    $datos = explode(":", trim($linea)); // con explode separamos los datos de la línea en un array
                    if (count($datos) == 2) { // aseguramos que la línea tenga el formato correcto
                        echo "<form action='mainAdmin.php' method='post'>"; // creamos un formulario
                        echo "<input type='hidden' name='juegoEliminar' value='$datos[0]'>"; // guardamos el juego en un input hidden
                        echo "<input type='hidden' name='consolaEliminar' value='$datos[1]'>"; // guardamos la consola en un input hidden
                        echo "<tr><td style='padding:10px'>" . $datos[0] . "</td><td style='padding:10px'>" . $datos[1] . '</td>';
                        if(file_exists("alquileres.txt")){ // si existe el archivo alquileres.txt
                            $archivoAlquileres = fopen("alquileres.txt", "r"); // abrimos el archivo en modo lectura
                            $alquilado = false; // inicializamos la variable a false
                            while (($lineaAlquiler = fgets($archivoAlquileres)) !== false) { // mientras no lleguemos al final del archivo
                                $datosAlquiler = explode(":", trim($lineaAlquiler)); // con explode separamos los datos de la línea en un array
                                if (count($datosAlquiler) == 2) { // aseguramos que la línea tenga el formato correcto
                                    if($datosAlquiler[1] == $datos[0]){ // si el juego está alquilado
                                        echo "<td style='padding:10px'>$datosAlquiler[0]</td>"; // mostramos el usuario que lo tiene alquilado
                                        $alquilado = true; // cambiamos la variable a true
                                    }
                                }
                            }
                            if(!$alquilado){ // si el juego no está alquilado
                                echo "<td style='padding:10px'>-</td>"; // mostramos un guión
                            }
                            fclose($archivoAlquileres); // cerramos el archivo
                        }else{
                            echo "<td style='padding:10px'>-</td>"; // si no existe el archivo, mostramos tambien un guión
                        }
                        echo '<td><input type="submit"  name="action" value="X" class="eliminar"></td></tr></form>'; // mostramos los datos
                    }
                }
                echo "</table>";
                fclose($archivoJuegos); // cerramos el archivo
            } else {
                echo "<p>No hay juegos registrados</p>"; // si no existe el archivo, mostramos un mensaje
            }
            ?>
        </div>
        <div class="contenedor2">
            <form action="mainAdmin.php" method="post">
                <h2 id="subtitulo">Añadir Videojuegos</h2>
                <label>Nombre del juego</label>
                <input type="text" name="nuevoJuego"><br><br>
                <label>Videoconsola</label><br><br>
                <select name="consola[]" multiple="true">
                    <option value="PS5">PS5</option>
                    <option value="PS4">PS4</option>
                    <option value="Xbox One">Xbox One</option>
                    <option value="Nintendo Switch">Nintendo Switch</option>
                    <option value="PC">PC</option>
                </select><br><br>
                <input type="submit" name="action" value="Añadir Juego" class="botones">
                <?php
                // Mostrar mensaje de error si los datos están vacíos
                if (isset($mensajeError)) {
                    echo "<p style='color:red;'>$mensajeError</p>";
                }
                
                ?>
            </form>
        </div>
    </div>
    <form action="login.php" method="post"><br>
        <input type="submit" name="action" value="Cerrar Sesion" class="botones">
    </form>
</body>
</html>