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
            return ['st' => 1, 'ses' => 'adm', 'usr' => 'root', 'nam' => 'Administrador'];
        } else if ($usuario != 'root') {
            // Si no es el usuario root se procesan las credenciales para determinar si el usuario es administrativo o cliente
            $usuario    = strtoupper($data['usuario']);

            try {
                // Consultar vendedores
                $stmt_ven   = $this->conn->query("SELECT co_ven, ven_des, login, password, email, condic FROM vendedor WHERE login = '$usuario'");
                $ven        = $stmt_ven->fetch(PDO::FETCH_ASSOC);

                if ($ven) {
                    if ($clave == trim($ven['password'])) {
                        // Si la clave es correcta se envian los datos necesarios para la sesion
                        return ['st' => 1, 'ses' => 'adm', 'usr' => $usuario, 'nam' => trim($ven['ven_des']), 'coVen' => trim($ven['co_ven'])];
                    } else {
                        return ['st' => 400, 'ses' => false];
                    }
                } else {
                    // Consultar clientes
                    $stmt_cli   = $this->conn->query("SELECT co_cli, cli_des, login, Password, co_ven, inactivo FROM clientes WHERE login = '$usuario' ");
                    $cli        = $stmt_cli->fetch(PDO::FETCH_ASSOC);

                    if ($cli) {
                        if ($clave == trim($cli['password'])) {
                            // Si la clave es correcta se envian los datos necesarios para la sesion
                            return ['st' => 2, 'ses' => 'cli', 'usr' => $usuario, 'nam' => trim($cli['cli_des']), 'coCli' => $cli['co_cli'], 'coVen' => trim($cli['co_ven']), 'inactivo' => $cli['inactivo']];
                        } else {
                            return ['st' => 400, 'ses' => false];
                        }
                    }
                }
            } catch (PDOException $e) {
                return "HA OCURRIDO UN ERROR: " . $e->getMessage();
            }
        } else {
            return ['st' => 400, 'ses' => false];
        }
    }

    public function cerrar_sesion(array $data)
    {
    }

    public function consultar_clientes(array $data)
    {

        // Activar filtro de vendedor si esta incluido
        $filtro = isset($data['coVen']) ? "WHERE co_ven LIKE '" . trim($data['coVen']) . "%'" : NULL;

        try {
            $stmt   = $this->conn->query("SELECT co_cli, cli_des, login, Password, co_ven, inactivo FROM clientes $filtro ");
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
