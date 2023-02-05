<?php
error_reporting(E_ALL);
date_default_timezone_set('America/Caracas');

// ==================================================================================
// ****************************** JBPassport WEB SERVICE *****************************
// ==================================================================================

// ========================================================================================= HEADERS

header("Access-Control-Allow-Origin: http://jbpassport.com");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 120");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    header("HTTP/1.1 200 OK");
    die();
}

// ========================================================================================= AUTENTICACION DE USUARIO
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Acceso no autorizado, sin credenciales de acceso."');
    header('HTTP/1.1 401 Unauthorized');
    echo 'Acceso no autorizado';
    exit(401);
} else {
    if ($_SERVER['PHP_AUTH_USER'] == 'JBPassport' && $_SERVER['PHP_AUTH_PW'] == 'Moscow777!!!') {
        // ========================================================================================= CARGAR CLASES AUTOMATICAMENTE
        spl_autoload_register(function ($class) {
            include("classes/{$class}.php");
        });

        // ========================================================================================= CONEXION BD SQL_SRV
        $conexion = new Conexion('config/config.json');
        $conn     = $conexion->conn_sqlsrv();

        if (@$conexion->error) {
            echo $conexion->error;
        }

        // ========================================================================================= RESPUESTA
        if (isset($_POST)) {

            // CONSULTAS
            $metodo = $_POST['met'];
            $proceso = new Proceso($conn);
            $respuesta = $proceso->$metodo($_POST);

            // ENVIAR RESPUESTA
            if ($respuesta) {
                http_response_code(200);
                echo json_encode(['codigoHTTP' => 200, 'dat' => $respuesta]);
            } else {
                http_response_code(400);
                header('HTTP/1.1 400 consulta invalida, el parametro indicado no arrojo ningun resultado.');
                echo json_encode(['codigoHTTP' => 400, 'dat' => '(HTTP 400) consulta invalida, el parametro indicado no arrojo ningun resultado.']);
            }
        } else {
            http_response_code(400);
            header('HTTP/1.1 400 consulta invalida, los parametro no pueden estar vacios.');
            echo json_encode(['codigoHTTP' => 400, 'dat' => '(HTTP 400) consulta invalida, los parametro no pueden estar vacios.']);
        }
    } else {
        header('WWW-Authenticate: Basic realm="Acceso no autorizado, credenciales incorrectas.');
        header('HTTP/1.1 401 Unauthorized');
        echo 'Acceso no autorizado';
        exit(401);
    }
}
