<?php
require_once(__DIR__ . "/../globals.php");
require_once(__DIR__ . "/../includes/db.php");
require_once(__DIR__ . "/../includes/header.php");
require_once(__DIR__ . "/../dao/RouteDAO.php");

$routeDAO = new RouteDAO($dbConn);
$routeList = $routeDAO->listRoutes(null, true, $_SESSION["activeUser"]["id"]);
if(!empty($routeList)) {
    $loadedData = true;
}
?>
<div class="container">
    <?php include(__DIR__ . "/../includes/offcanva-menu.php"); ?>
    <h1 class="d-inline-flex justify-content-start mt-3">Minhas rotas</h1>

    <div class="container">
        <?php if(isset($loadedData)) : ?>
        <table class="table align-middle text-center table-striped">
            <thead>
                <th scope="col">Rota</th>
                <th scope="col">Status</th>
                <th scope="col">Detalhes</th>
            </thead>
            <tbody class="table-group-divider">
            <?php foreach($routeList as $routeItem) : ?>
                <tr scope="row">
                    <td class="align-self-center"><?= $routeItem["id"] ?></td>
                    <td><span class="badge rounded-pill text-bg-secondary"><?= $routeItem["status"] ?></span></td>
                    <td><a class="btn btn-primary btn-sm" href="<?= $BASE_URL ?>delivery/routedetail.php?routeid=<?= $routeItem["id"] ?>"><i class="bi bi-eye-fill"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php
require_once(__DIR__ . "/../includes/footer.php");