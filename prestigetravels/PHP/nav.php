
<nav class="navbar navbar-expand-lg custom-nav" style="background-color: #131921;">
    <div class="container-fluid d-flex align-items-center">
        <div class="col-md-1 col-lg-1">
            <a href="index.php" class="navbar-brand">
                <img src='../IMG/logo.png' alt="logo" width="50" height="50" />
            </a>
        </div>
        <div class="col-md-5 col-lg-5 offset-md-2 d-flex  align-items-center">
            <form method="GET" action="busqueda.php" class="d-flex input-group">
                <div class="dropdown filter-btn me-1">

                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-sliders2"></i>
                        Filtros
                    </button>

                    <ul class="dropdown-menu dropdown-menu w-auto">
                        <div class="col">
                            <label>Fecha de salida:</label>
                            <input type="date" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col">
                            <label>Fecha de llegada:</label>
                            <input type="date" name="fecha_fin" class="form-control">
                        </div>
                        <div class="col">
                            <label>Ciudad:</label>
                            <input type="search" id="form1" name="ciudad" class="form-control" placeholder="Ciudad" />
                        </div>
                    </ul>
                </div>
                <div class="col ms-1 d-flex align-items-center">
                    <input type="search" id="form1" name="search" class="form-control search" style="height:40px" placeholder="Buscar" />
                    <button type="submit" class="btn btn-primary search-btn" style="width: 50px">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <input type="hidden" name="Avanzada" value="0">
            </form>
            <div class="col-md-3 col-lg-3 ms-4">
                <a href="busquedaAvanzada.php" class="btn btn-primary truncate-text2">Busqueda avanzada</a>
            </div>
        </div>

        <div class="d-flex col-md-3 col-lg-3 justify-content-end">

            <?php
            if (isset($_SESSION["desc"])) {
                if ($_SESSION["desc"]) {
                    echo "<a href='carrito.php' class='btn btn-danger me-2 oferta truncate-text2'>Descuento disponible</a>";
                }
            }

            ?>

            <a href="carrito.php" class="btn btn-secondary me-2"><i class="bi bi-cart"></i></a>

            <div class="dropdown">
                <?php if (isset($_SESSION["rut"])) : ?>
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="truncate-text"><?php echo $_SESSION["nombre"] ?></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end w-auto">
                        <li><a class="dropdown-item" href="profile.php">Perfil</a></li>
                        <li><a class="dropdown-item" href="wishList.php">Wish list</a></li>
                        <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                    </ul>
                <?php else : ?>
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end w-auto">
                        <li><a class="dropdown-item" href="login.php">Log in</a></li>
                        <li><a class="dropdown-item" href="SignUp.php">Sign up</a></li>
                    </ul>
                <?php endif ?>
            </div>
        </div>
    </div>
</nav>