<?php

// Configuración de conexión
$usuario = ''; // Reemplaza con tu usuario de Oracle
$contrasena = ''; // Reemplaza con tu contraseña de Oracle
$cadenaConexion = ''; // Cadena de conexión directa

// Conexión a la base de datos
$conn = oci_connect($usuario, $contrasena, $cadenaConexion);

if (!$conn) {
    $e = oci_error();
    echo "Error al conectar a la base de datos: " . $e['message'];
    exit;
}

// Consulta SQL
// $sql = "SELECT * FROM TUSUARIOS WHERE FHASTA = FNCFHASTA()";
$sql = "SELECT SALDOMONEDACUENTA AS VALOR, FDESDE AS FECHA FROM TSALDOS WHERE CCUENTA = '0091561100' AND CATEGORIA = 'DEPVEF' AND PRINCIPAL =  1 ORDER BY FDESDE DESC";

// Preparar y ejecutar la consulta
$stid = oci_parse($conn, $sql);
oci_execute($stid);

// Mostrar resultados en una tabla HTML con estilos y atributos únicos
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";

// Obtener y mostrar encabezados de la tabla
$currentRow = oci_fetch_assoc($stid); // Renombrar variable
if ($currentRow) {
    echo "<tr style='background-color: #f2f2f2; text-align: left;'>";
    $columnIndex = 0; // Índice para generar IDs únicos
    foreach (array_keys($currentRow) as $columnName) {
        echo "<th id='header-$columnIndex' style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($columnName) . "</th>";
        $columnIndex++;
    }
    echo "</tr>";

    // Mostrar la primera fila
    $rowIndex = 0; // Índice para las filas
    echo "<tr style='background-color: #ffffff;'>";
    $columnIndex = 0;
    foreach ($currentRow as $value) {
        echo "<td id='row-{$rowIndex}-col-{$columnIndex}' style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($value ?? '') . "</td>";
        $columnIndex++;
    }
    echo "</tr>";

    // Mostrar las filas restantes
    while ($row = oci_fetch_assoc($stid)) {
        $rowIndex++;
        echo "<tr style='background-color: #f9f9f9;'>";
        $columnIndex = 0;
        foreach ($row as $value) {
            echo "<td id='row-{$rowIndex}-col-{$columnIndex}' style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($value ?? '') . "</td>";
            $columnIndex++;
        }
        echo "</tr>";
    }
}

echo "</table>";

// Liberar recursos y cerrar conexión
oci_free_statement($stid);
oci_close($conn);