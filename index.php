<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

define("HOST","127.0.0.1");
define("USUARIO","root");
define("PASSWORD","root");
define("NAMEDB","biblioteca");
define("PUERTO","3306");

$conexion = mysqli_connect(HOST, USUARIO, PASSWORD, NAMEDB, PUERTO);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$sql = "SELECT * FROM libros";
$resultado = $conexion->query($sql);

if ($resultado === false) {
    die("Error en la consulta: " . $conexion->error);
}

if ($resultado->num_rows > 0) {
    $html = '<h1>Datos de la tabla</h1>';
    $html .= '<table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Género</th>
                        <th>ID Autor</th>
                        <th>Número de Páginas</th>
                        <th>Número de Ejemplares</th>
                    </tr>
                </thead>
                <tbody>';

    while ($fila = $resultado->fetch_assoc()) {
        $html .= '<tr>
                    <td>'.$fila['Titulo'].'</td>
                    <td>'.$fila['Genero'].'</td>
                    <td>'.$fila['idAutor'].'</td>
                    <td>'.$fila['NumeroPaginas'].'</td>
                    <td>'.$fila['NumeroEjemplares'].'</td>
                </tr>';
    }
    $html .= '</tbody></table>';
} else {
    $html = '<h1>No hay datos en la tabla</h1>';
}

// Configurar DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Cargar el contenido HTML
$dompdf->loadHtml($html);

// Configurar tamaño de página
$dompdf->setPaper('A4', 'landscape');

// Renderizar el PDF
$dompdf->render();

// Enviar el PDF al navegador
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="tabla_datos.pdf"');
echo $dompdf->output();

$conexion->close();
?>