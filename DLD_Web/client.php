<?php
    require_once("globals.php");
    require_once("models/Messages.php");
    require_once("includes/db.php");
    require_once("dao/ClientDAO.php");

    $message = new Message($BASE_URL);
    $clients = new ClientDAO($dbConn);

    if (!isset($_SESSION["activeUser"])) {
        $message->setMessage("Faça login para acessar", "danger");
    }

    switch ($_SERVER["REQUEST_METHOD"]) {

        case "GET":
            if (isset($_GET["page"])){
                $actualPage = intval(filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT));

                if (isset($_COOKIE["_dld-filterPagination"])) {

                    $cookieValue = json_decode($_COOKIE["_dld-filterPagination"], true);
                    $optSearch = $cookieValue["optSearch"];
                    $txtSearch = $cookieValue["txtSearch"];

                    $offset = ($actualPage - 1) * 10;
                    $allClients = $clients->searchClient($optSearch, $txtSearch, $offset);
                    $pagination = true;
                    
                } else {

                    $offset = ($actualPage - 1) * 10;
                    $allClients = $clients->listClients($offset);
                    $pagination = true;
                }
            } else {

                if (isset($_COOKIE['_dld-filterPagination'])) {
                    setcookie('_dld-filterPagination', "", time() - 3600);
                }
        
                $allClients = $clients->listClients();

                if ($allClients["qty"] > 10) {
                    $pagination = true;
                }
            }
            break;
        
        case "POST":
            
            if ($_POST["action"] == "filter" && !empty($_POST["txtSearch"])) {
                $optSearch = filter_input(INPUT_POST, "optSearch");
                $txtSearch = filter_input(INPUT_POST, "txtSearch");

                $allClients = $clients->searchClient($optSearch, $txtSearch);

                if ($allClients["qty"] > 10) {
                    $pagination = true;

                    $cookie = ["optSearch" => $optSearch, "txtSearch" => $txtSearch];
                    setcookie("_dld-filterPagination", json_encode($cookie));
                }
            } else {

                $allClients = $clients->listClients();
                
                if ($allClients["qty"] > 10) {
                    $pagination = true;
                }
            }
            break;
            
        default:
            
            $allClients = $clients->listClients();
            
            if ($allClients["qty"] > 10) {
                $pagination = true;
            }
            break;
    }

    if(isset($pagination)) {

        if (!isset($actualPage)) {
            $actualPage = 1;
        }

        $maxNumberOfPages = 10;
        $totalPages = intval(ceil($allClients["qty"] / $maxNumberOfPages));
        
        $initialPage = $actualPage - ($maxNumberOfPages / 2);
        $finalPage = $actualPage + ($maxNumberOfPages / 2);

        if ($initialPage < 1) {
            $initialPage = 1;
        }

        if ($finalPage > $totalPages) {
            $finalPage = $actualPage + ($totalPages - $actualPage);
        } elseif ($finalPage < $maxNumberOfPages) {
            $finalPage = $finalPage + ($maxNumberOfPages - $finalPage);
        }
    }

    $returnMessage = $message->getMessage();

    if (!empty($returnMessage)) {
        $message->clearMessage();
    }

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/bootstrap.css">
    <link rel="stylesheet" href="<?= $BASE_URL ?>/bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/style.css">
    <link rel="shortcut icon" href="<?= $BASE_URL ?>imgs/favicon-dld.png" type="image/png">
</head>
<body>
    <header>
        <nav class="navbar" id="navigation-bar">
            <div class="container-fluid">
                <div id="nav-logo">
                    <img src="<?= $BASE_URL ?>imgs/favicon-dld.png" alt="DL Delivery WEB" class="navbar-brand">
                    <span class="navbar-brand"><?= APP_NAME ?></span>
                </div>
            </div>
        </nav>
    </header>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <div id="page-content" class="col-auto col-md-9">
            <div class="container mt-5">

                <?php if (!empty($returnMessage["msg"])) : ?>
                    <div class="container mt-3" id="alert-box">
                        <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                            <?= $returnMessage["msg"] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="container mb-3">
                    <div class="row">
                        <div class="col-9">
                            <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
                                <input type="hidden" name="action" value="filter">

                                <div class="row">
                                    <div class="col-3">
                                        <select class="form-select" name="optSearch" id="optSearch">
                                            <option value="id">ID</option>
                                            <option value="name" selected>Nome</option>
                                        </select>
                                    </div>

                                    <div class="col-9">
                                        <div class="input-group">
                                            <input class="form-control" type="text" name="txtSearch" id="txtSearch" placeholder="Critério de busca">
                                            <input class="btn btn-primary" type="submit" value="Buscar">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col d-flex align-items-center justify-content-end">
                            <a href="<?= $BASE_URL ?>addclient.php" class="btn btn-success">
                                <i class="bi bi-person-fill-add"></i>
                                <span class="ms-2">Novo Cliente</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <?php if(isset($_COOKIE["_dld-filterPagination"]) || (isset($_POST["action"]) && !empty($_POST["txtSearch"]))): ?>
                    <span>Exibindo resultados da busca por <?= $optSearch == "id" ? "ID" : "Nome" ?> contendo "<?= $txtSearch ?>":</span>
                <?php endif; ?>
                <table class="table align-middle table-striped">
                    <thead>
                        <th scope="col">Id</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Ação</th>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($allClients["results"] as $client) : ?>
                            <tr scope="row">
                                <td><?= $client["id"] ?></td>
                                <td><a id="actionMenu" href="location.php?clie=<?= $client["id"] ?>"><?= $client["name"] ?></a></td>
                                <td><a class="btn btn-primary btn-sm" href="editclient.php?id=<?= $client["id"] ?>"><i class="bi bi-pencil-fill"></i></a> <a class="btn btn-danger btn-sm" href="delclient.php?id=<?= $client["id"] ?>"><i class="bi bi-trash-fill"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Start Pagination -->
            <?php if(isset($pagination)): ?>
                <div class="container d-flex justify-content-center">
                    <?php if ($totalPages > 1) : ?>
                        <nav aria-label="Páginas">
                            <ul class="pagination">
                                <li class="page-item <?= ($initialPage - 1) < 1 ? "disabled" : "" ?>">
                                    <a class="page-link" href="<?= ($initialPage - 1) < 1 ? "#" : "client.php?page=" . $initialPage - 1; ?>" aria-label="Anterior"><span aria-hidden="true">&laquo;</span></a>
                                </li>

                                <?php for ($page = $initialPage; $page <= $finalPage; $page++) : ?>
                                    <li class="page-item <?php if ($page == $actualPage) : ?> active <?php endif; ?>"><a class="page-link" href="client.php?page=<?= $page ?>"><?= $page ?></a></li>
                                <?php endfor; ?>

                                <li class="page-item <?= ($finalPage + 1) > $totalPages ? "disabled" : "" ?>">
                                    <a class="page-link" href="<?= ($finalPage + 1) > $totalPages ? "#" : "client.php?page=" . $finalPage + 1; ?>" aria-label="Próximo"><span aria-hidden="true">&raquo;</span></a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <!-- End Pagination -->
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>