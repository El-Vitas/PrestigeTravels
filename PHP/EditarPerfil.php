<?php
include("connection.php");
$UserErr = $nameErr = $emailErr = $rutErr = $dateErr = "";

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (array_key_exists('Editar', $_POST)) {
        $send = true;

        $name = trim($_POST["nombre"]);
        $email = $_POST["email"];
        $date = $_POST["fecha"];

        if (!preg_match("/^[a-zA-z ]+$/", $name)) {
            $nameErr = "nombre no valido";
            $send = false;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "ingresar un email valido";
            $send = false;
        }
        if (!validateDate($date, 'Y-m-d')) {
            $dateErr = "fecha invalida";
            $send = false;
        }

        $sql;
        if ($_SESSION["correo"] != $email) {
            $sql = "SELECT * FROM usuarios WHERE correo = '$email'";
        }

        if (isset($sql)) {
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $UserErr = "correo ya esta registrado";
                $send = false;
            }
        }

        if ($send) {
            $sql = $conn->prepare("UPDATE usuarios SET nombre = ?, fecha_nacimiento = ?, correo = ? WHERE rut = ?");
            $sql->bind_param("ssss", $name, $date, $email, $_SESSION['rut']);
            $sql->execute();

            $_SESSION["nombre"] = $name;
            $_SESSION["correo"] = $email;
            $_SESSION["fecha_nacimiento"] = $date;

            header('Location: http://localhost/Lab2/PHP/profile.php');
        }
    }
}
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (array_key_exists('Eliminar', $_POST)) {
        $sql = "DELETE FROM usuarios WHERE rut = ?";
        $preReq = $conn->prepare($sql);
        $preReq->bind_param("s", $_SESSION['rut']);
        $preReq->execute();

        session_start();
        session_unset();
        header('Location: http://localhost/Lab2/PHP/profile.php');
    }
}
?>

<html>

<head>
    <?php
    include("head.php");
    ?>
    <title>SignUp</title>
</head>

<body>
    <header>
        <?php
        include("nav.php");
        ?>
    </header>

    <div class="container mt-2 align-items-center bg-white p-5 rounded-5 text-secondary shadow r me-auto ms-auto" style="width: 25rem">

        <form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
            <span><?php echo $UserErr; ?></span>

            <div class="text-center fs-1 fw-bold">Editar perfil</div>

            <div class="form-group">
                <div class="col-12">
                    <label for="nombre">Nombre</label>
                </div>
                <input type="text" class="form-control bg-light" name="nombre" placeholder="Ingresar Nombre" value="<?php echo $_SESSION['nombre'] ?>">
                <div class="col-12">
                    <span><?php echo $nameErr; ?></span>
                </div>
            </div>

            <div>
                <div class="col-12">
                    <label for="fecha">Fecha de nacimiento</label>
                </div>
                <input type="date" name="fecha" class="form-control bg-light" value="<?php echo $_SESSION['fecha_nacimiento'] ?>">
                <div class="col-12">
                    <span><?php echo $dateErr; ?></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12">
                    <label for="email">Email</label>
                </div>
                <input type="email" class="form-control bg-light" name="email" placeholder="Ingresar email" value="<?php echo $_SESSION['correo'] ?>">
                <div class="col-12">
                    <span><?php echo $emailErr; ?></span>
                </div>
            </div>

            <input class="btn btn-primary text-white w-100 mt-4 fw-semibold shadow-sm" type="submit" name="Editar" value="Editar Perfil" />
        </form>

        <form action=<?php echo $_SERVER["PHP_SELF"] ?> method="post">
            <input class="btn btn-danger text-white w-100 mt-4 fw-semibold shadow-sm" type="submit" name="Eliminar" value="Eliminar Cuenta" />
        </form>
    </div>
</body>

</html>