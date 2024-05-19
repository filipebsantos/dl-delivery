<?php
require_once("includes/header.php");
require_once("includes/db.php");
require_once("dao/RouteDAO.php");
require_once("dao/UserDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

$users = new UserDAO($dbConn);
$userList = $users->listUsers();

if (!isset($_SESSION["filteredRoutes"])) {
    $routes = new RouteDAO($dbConn);
    $routeList = $routes->listRoutes();
} else {
    $routeList = $_SESSION["filteredRoutes"];
    unset($_SESSION["filteredRoutes"]);
}

?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <div id="page-content" class="col-auto col-md-7">
            <div class="container mt-5">
                <?php if (!empty($returnMessage["msg"])) : ?>
                    <div class="container mt-3" id="alert-box">
                        <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                            <?= $returnMessage["msg"] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row row-cols-2">
                    <div class="col">
                        <div class="container border rounded shadow-sm p-3">
                            <h5>Criar rota</h5>

                            <form action="routeprocess.php" method="post">
                                <input type="hidden" name="action" value="create">
                                <input type="hidden" name="createdBy" value="<?= $_SESSION["activeUser"]["id"] ?>">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="optDeliveryman">Entregador</span>
                                    <select class="form-select" name="optDeliveryman" id="optDeliveryman" autocomplete="on" required>
                                        <?php foreach ($userList as $user) : ?>
                                            <option value="<?= $user["id"] ?>"><?= $user["firstname"] . " " . $user["lastname"] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button class="btn btn-success" type="submit"><i class="bi bi-plus-circle-fill"></i> Nova rota</button>
                            </form>
                        </div>
                    </div>

                    <div class="col">
                        <div class="container border rounded shadow-sm p-3">
                            <h5>Filtrar rotas</h5>

                            <form action="routeprocess.php" method="post">
                                <input type="hidden" name="action" value="filter">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="optDeliveryman">Entregador</span>
                                    <select class="form-select" name="optDeliveryman" id="optDeliveryman" autocomplete="on" required>
                                        <option value="0" selected>Todos</option>
                                        <?php foreach ($userList as $user) : ?>
                                            <option value="<?= $user["id"] ?>"><?= $user["firstname"] . " " . $user["lastname"] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text" id="txtDate">Data</span>
                                    <input class="form-control" type="date" name="txtDate" id="txtDate" value="">
                                    <button class="btn btn-success" type="submit"><i class="bi bi-funnel-fill"></i> Filtrar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="container text-center mt-3">
                    <table class="table align-middle table-striped">
                        <thead>
                            <th scope="col">Rota</th>
                            <th scope="col">Entregador</th>
                            <th scope="col">Hora Início</th>
                            <th scope="col">Hora Fim</th>
                            <th scope="col">Status</th>
                            <th scope="col">Ação</th>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php foreach ($routeList as $route) : ?>
                                <tr scope="row">
                                    <td><?= $route["id"] ?></td>
                                    <td><?= $route["fullname"] ?></td>
                                    <td><?= !empty($route["starttime"]) ? substr($route["starttime"], 0, 8) : "" ?></td>
                                    <td><?= !empty($route["endtime"]) ? substr($route["endtime"], 0, 8) : "" ?></td>
                                    <td><span class="badge rounded-pill text-bg-secondary"><?= $route["status"] ?></span></td>
                                    <?php if ($route["status"] == "INICIADA" || $route["status"] == "FINALIZADA") : ?>
                                        <td><a class="btn btn-primary btn-sm" href="editroute.php?routeid=<?= $route["id"] ?>"><i class="bi bi-pencil-fill"></i></a></td>
                                    <?php else : ?>
                                        <td><a class="btn btn-primary btn-sm" href="editroute.php?routeid=<?= $route["id"] ?>"><i class="bi bi-pencil-fill"></i></a> <a class="btn btn-danger btn-sm" href="delroute.php?routeid=<?= $route["id"] ?>"><i class="bi bi-trash-fill"></i></a></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>