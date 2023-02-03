<?php


class Proceso
{
    public $conn = NULL;

    function __construct($conexSQLsrv)
    {
        $this->conn = $conexSQLsrv;
    }

    public function validar_datos(array $data)
    {
        return json_encode($data);
    }

    public function iniciar_sesion(array $data)
    {
        // Res = 1 (amin), 2 (Cliente), 400 (Error)

        $usuario    = strtolower($data['usuario']);
        $clave      = $data['clave'];
        $d = date('d');

        if ($usuario == 'root' && $clave == 'r007_' . $d) {
            $_SESSION['admin'] = true;
            return ['st' => 1, 'sesion' => 'admin'];
        } else {
            return ['st' => 400, 'sesion' => false];
        }
    }

    public function cerrar_sesion(array $data)
    {
    }

    public function consultar_clientes()
    {
        try {
            $stmt   = $this->conn->query("SELECT co_cli, cli_des, login, Password, co_ven, inactivo FROM clientes");
            $row    = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            return "HA OCURRIDO UN ERROR: " . $e->getMessage();
        }
    }

    public function consultar_unico_cliente(array $datos)
    {
        $coCli = $datos['coCli'];

        try {
            $stmt   = $this->conn->query("SELECT co_cli, cli_des, login, Password, co_ven, inactivo FROM clientes WHERE co_cli = '$coCli'");
            $row    = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            return "HA OCURRIDO UN ERROR: " . $e->getMessage();
        }
    }

    public function consultar_vendedores()
    {
        try {
            $stmt   = $this->conn->query("SELECT co_ven, ven_des, login, password, email, condic FROM vendedor");
            $row    = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row;
        } catch (PDOException $e) {
            return "HA OCURRIDO UN ERROR: " . $e->getMessage();
        }
    }

    public function actualizar_usr_pass_cli(array $datos)
    {
        $login = trim($datos['usuario']);
        $pass  = trim($datos['clave']);
        $coCli = $datos['coCli'];

        try {
            $this->conn->query("UPDATE clientes SET login='$login', Password='$pass' WHERE co_cli = '$coCli'");
            return 'Datos actualizados';
        } catch (PDOException $e) {
            return "HA OCURRIDO UN ERROR: " . $e->getMessage();
        }
    }

    public function actualizar_usr_pass_ven(array $datos)
    {
        $login = trim($datos['usuario']);
        $pass  = trim($datos['clave']);
        $coVen = $datos['coVen'];

        try {
            $this->conn->query("UPDATE vendedor SET login= '$login', Password='$pass' WHERE co_ven = '$coVen'");
            return 'Datos actualizados';
        } catch (PDOException $e) {
            return "HA OCURRIDO UN ERROR: " . $e->getMessage();
        }
    }

    function __destruct()
    {
    }
}
