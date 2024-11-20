<?php
session_start(); // Iniciamos la sesión

// Función para eliminar una línea de un archivo
function eliminarLinea($archivo, $lineaEliminar) {
    $archivoOriginal = fopen($archivo, "r");
    $archivoTemporal = fopen("temp.txt", "w");

    while (($linea = fgets($archivoOriginal)) !== false) {
        if (trim($linea) != $lineaEliminar) {
            fwrite($archivoTemporal, $linea); // mientras la línea no sea la que queremos eliminar, la escribimos en el archivo temporal
        }
    }

    fclose($archivoOriginal);
    fclose($archivoTemporal);

    // Reemplazar el archivo original con el archivo temporal
    rename("temp.txt", $archivo);
}

// Si pulsamos en alquilar, creamos una nueva línea en alquileres.txt con el usuario:juego
if (isset($_POST['action']) && $_POST['action'] == "Alquilar") {
    $archivoAlquileres = fopen("alquileres.txt", "a"); // abrimos el archivo en modo escritura a para no sobreescribir
    fwrite($archivoAlquileres, $_SESSION['usuario'] . ":" . $_POST['juego'] . "\n"); // escribimos el usuario y el juego en el archivo
    fclose($archivoAlquileres); // cerramos el archivo
    header("Location: mainCliente.php"); // redirigimos a la página principal
    exit(); // Asegura que el script se detenga después de la redirección
}

// Si pulsamos en devolver, eliminamos la línea del archivo alquileres.txt con la función eliminarLinea
if (isset($_POST['action']) && $_POST['action'] == "Devolver") {
    eliminarLinea("alquileres.txt", $_SESSION['usuario'] . ":" . $_POST['juego']); // llamamos a la función eliminarLinea
    header("Location: mainCliente.php"); // redirigimos a la página principal
    exit(); // Asegura que el script se detenga después de la redirección
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alquiler</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <h1 id="titulo">Alquileres disponibles</h1>
    <?php
    echo "<div class='contenedor'>
          <h2 id='subtitulo'>Bienvenido " . $_SESSION['usuario'] . "</h2>
          <p>En el siguiente catálogo encontrarás los videojuegos disponibles para alquilar</p>
          </div>"; // Mostramos el nombre de administrador y una introducción a la página
    ?>
    <div class="contenedor">        
            <?php
            // Aquí mostraré los juegos introducidos y si estos están alquilados por algún usuario o no (-)
            // Ahora voy a leer el archivo juegos.txt y mostraré los juegos y consolas
            // Para esto primero compruebo si el archivo existe
            if (file_exists("juegos.txt")) {
                $archivoJuegos = fopen("juegos.txt", "r"); // abrimos el archivo en modo lectura
                echo "<table style='display:flex; justify-content: center;'>
                      <tr><td style='padding:10px'><u>Juego</u></td><td style='padding:10px'><u>Plataforma</u></td><td style='padding:10px'><u>Estado</u></td><td>Acción</td></tr>";
                while (($linea = fgets($archivoJuegos)) !== false) { // mientras no lleguemos al final del archivo
                    $datos = explode(":", trim($linea)); // con explode separamos los datos de la línea en un array
                    if (count($datos) == 2) { // aseguramos que la línea tenga el formato correcto                        
                        echo "<tr><td style='padding:10px'>" . $datos[0] . "</td><td style='padding:10px'>" . $datos[1] . '</td>';
                        if (file_exists("alquileres.txt")) { // si existe el archivo alquileres.txt
                            $archivoAlquileres = fopen("alquileres.txt", "r"); // abrimos el archivo en modo lectura
                            $alquilado = false; // inicializamos la variable a false
                            while (($lineaAlquiler = fgets($archivoAlquileres)) !== false) { // mientras no lleguemos al final del archivo
                                $datosAlquiler = explode(":", trim($lineaAlquiler)); // con explode separamos los datos de la línea en un array
                                if (count($datosAlquiler) == 2) { // aseguramos que la línea tenga el formato correcto
                                    if ($datosAlquiler[1] == $datos[0]) { // si el juego está alquilado
                                        echo "<td style='padding:10px; color:red'>Alquilado</td>"; // mostramos alquilado
                                        if ($datosAlquiler[0] == $_SESSION['usuario']) { // si el juego está alquilado por el usuario actual
                                            echo '<td style="padding:10px">
                                                    <form action="mainCliente.php" method="post" onsubmit="return confirm(\'¿Estás seguro de que deseas devolver este juego?\');">
                                                        <input type="hidden" name="juego" value="' . $datos[0] . '">
                                                        <input type="submit" name="action" value="Devolver" class="eliminar">
                                                    </form>
                                                  </td></tr>'; // Creamos un formulario para devolver el juego y mostramos el botón de devolver. Si lo pulsamos, se nos preguntará si estamos seguros de devolverlo con el onsubmit
                                        }
                                        $alquilado = true; // cambiamos la variable a true
                                    }
                                }
                            }
                            if (!$alquilado) { // si el juego no está alquilado
                                echo "<td style='padding:10px; color:green'>Disponible</td>"; // mostramos que está disponible                                 
                                echo '<td style="padding:10px">
                                        <form action="mainCliente.php" method="post" onsubmit="return confirm(\'¿Estás seguro de que deseas alquilar este juego?\');">
                                            <input type="hidden" name="juego" value="' . $datos[0] . '">
                                            <input type="submit" name="action" value="Alquilar" class="alquilar">
                                        </form>
                                      </td></tr>'; // Creamos un formulario para alquilar el juego y mostramos el botón de alquilar
                            }
                            fclose($archivoAlquileres); // cerramos el archivo
                        } else {
                            echo "<td style='padding:10px; color:green'>Disponible</td>"; // si no existe el archivo, mostramos tambien disponible
                            echo '<td style="padding:10px">
                                    <form action="mainCliente.php" method="post" onsubmit="return confirm(\'¿Estás seguro de que deseas alquilar este juego?\');">
                                        <input type="hidden" name="juego" value="' . $datos[0] . '">
                                        <input type="submit" name="action" value="Alquilar" class="alquilar">
                                    </form>
                                  </td></tr>'; // Creamos un formulario para alquilar el juego y mostramos el botón de alquilar
                        }                        
                    }
                }
                echo "</table>";
                fclose($archivoJuegos); // cerramos el archivo
            } else {
                echo "<p>No hay juegos disponibles</p>"; // si no existe el archivo, mostramos un mensaje
            }
            ?>        
    </div>
    <form action="login.php" method="post"><br>
        <input type="submit" name="action" value="Cerrar Sesion" class="botones">
    </form>
</body>

</html>