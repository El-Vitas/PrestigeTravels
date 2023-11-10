<?php
include("connection.php");
$UserErr = $nameErr = $passwordErr = $confPassErr = $emailErr = $rutErr = $dateErr = "";

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $send = true;

    $name = trim($_POST["nombre"]);
    $password = $_POST["password"];
    $email = $_POST["email"];
    $rut = trim($_POST["rut"]);
    $date = $_POST["fecha"];

    if (!preg_match("/^[a-zA-z ]+$/", $name)) {
        $nameErr = "nombre no valido";
        $send = false;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "ingresar un email valido";
        $send = false;
    }
    if (!preg_match("/^[0-9]{8}(-)([0-9])$/", $rut)) {
        $rutErr = "rut invalido";
        $send = false;
    }
    if (strlen($password) == 0) {
        $passwordErr = "contraseña invalida";
        $send = false;
    }
    if ($password != $_POST["Confpassword"]) {
        $confPassErr = "contraseñas no coinciden";
        $send = false;
    }
    if (!validateDate($date, 'Y-m-d')) {
        $dateErr = "fecha invalida";
        $send = false;
    }

    $sql = "SELECT * FROM usuarios WHERE rut = '$rut' or correo = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $UserErr = "rut o correo ya estan registrados";
        $send = false;
    }

    if ($send) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $rut = preg_replace("/-/", "", $rut);

        $sql = $conn->prepare("INSERT INTO usuarios (rut, nombre, fecha_nacimiento, correo, password) VALUES (?, ?, ?, ?, ?)");
        $sql->bind_param("sssss", $rut, $name, $date, $email, $password);
        $sql->execute();
        
        session_start();
        $_SESSION["rut"] = $rut;
        $_SESSION["nombre"] = $name;
        $_SESSION["correo"] = $email;
        $_SESSION["fecha_nacimiento"] = $date;

        $_SESSION["desc"] = false;
        $_SESSION["desc_"] = false;

        $descuento = rand(0, 10);
        if($descuento > 0){
            $_SESSION["desc"] = true;
        }

        header('Location: http://localhost/Lab2/PHP/index.php');
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

            <div class="text-center fs-1 fw-bold">Registro</div>
            <div class="input-group mt-2 ">
                <div class="col-12">
                    <label for="rut">Rut</label>
                </div>
                <input class="form-control bg-light" type="text" name="rut" placeholder="12345678-9" />
                <div class="col-12">
                    <span><?php echo $rutErr; ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12">
                    <label for="nombre">Nombre</label>
                </div>
                <input type="text" class="form-control bg-light" name="nombre" placeholder="Ingresar Nombre" value="<?php echo isset($_POST['nombre']) ? $_POST['nombre'] : '' ?>">
                <div class="col-12">
                    <span><?php echo $nameErr; ?></span>
                </div>
            </div>

            <div>
                <div class="col-12">
                    <label for="fecha">Fecha de nacimiento</label>
                </div>
                <input type="date" name="fecha" class="form-control bg-light" value="<?php echo isset($_POST['fecha']) ? $_POST['fecha'] : '' ?>">
                <div class="col-12">
                    <span><?php echo $dateErr; ?></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12">
                    <label for="email">Email</label>
                </div>
                <input type="email" class="form-control bg-light" name="email" placeholder="Ingresar email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
                <div class="col-12">
                    <span><?php echo $emailErr; ?></span>
                </div>
            </div>


            <div class="input-group mt-1">
                <div class="col-12">
                    <label for="password">Contraseña</label>
                </div>
                <input class="form-control bg-light" type="password" name="password" placeholder="Contraseña" />
                <div class="col-12">
                    <span><?php echo $passwordErr; ?></span>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12">
                    <label for="Confpassword">Confirmar Contraseña</label>
                </div>
                <input type="password" class="form-control bg-light" name="Confpassword" placeholder="Confirmar Contraseña">
                <div class="col-12">
                    <span><?php echo $confPassErr; ?></span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary text-white w-100 mt-4 fw-semibold shadow-sm">Submit</button>

            <div class="d-flex gap-1 justify-content-center mt-1">
                <div>¿Ya tienes cuenta?</div>
                <a href="login.php" class="text-decoration-none text-info fw-semibold">Login</a>
            </div>
        </form>
    </div>
</body>

</html>
