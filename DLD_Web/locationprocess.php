<?php
require_once("globals.php");
require_once("includes/db.php");
require_once("dao/ClientDAO.php");
require_once("dao/LocationDAO.php");
require_once("models/Messages.php");

$message = new Message($BASE_URL);
$clientDAO = new ClientDAO($dbConn);
$locationDAO = new LocationDAO($dbConn);

// Accept only posts requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["action"]) && filter_input(INPUT_POST, "action", FILTER_SANITIZE_SPECIAL_CHARS)) {
        $action = $_POST["action"];

        switch ($action) {
            case "find":

                if (isset($_POST["txtClientId"]) && !empty($_POST["txtClientId"])) {
                    $clieId = filter_input(INPUT_POST, "txtClientId", FILTER_SANITIZE_NUMBER_INT);

                    if ($clieId) {

                        try {
                            $client = $clientDAO->getClient($clieId);

                            if (!$client) {
                                $message->setMessage("Cliente não localizado!", "danger", "back");
                                exit;
                            }
                        } catch (Exception $error) {
                            $message->setMessage($error->getMessage(), "danger", "back");
                        }

                        try {

                            $location = $locationDAO->listLocations($clieId);
                        } catch (Exception $error) {

                            $message->setMessage($error->getMessage(), "danger", "back");
                        }

                        $returnQuery = [
                            "clieId" => $client["id"],
                            "clieName" => $client["name"],
                            "locations" => $location
                        ];

                        $_SESSION["locationReturn"] = $returnQuery;

                        if (str_contains(dirname($_SERVER["HTTP_REFERER"]), "/delivery")) {
                            header("Location: " . $BASE_URL . "delivery/routedetail.php?routeid=" . $_POST["routeid"]);
                            exit;
                        } else {

                            if (isset($_POST["origin"]) && $_POST["origin"] == "route") {
                                header("Location: " . $BASE_URL . "editroute.php?routeid=" . $_POST["routeid"]);
                                exit;
                            } else {
                                header("Location: " . $BASE_URL . "location.php");
                                exit;
                            }
                        }
                    } else {
                        $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                    }
                } else {
                    $message->setMessage("Informe o código do cliente", "danger", "back");
                }
                break;

            case "create":

                if ((isset($_POST["txtClientId"]) && isset($_POST["txtCoordinates"]) && isset($_POST["optNeighborhood"]) && isset($_POST["optLocationType"])) && (!empty($_POST["txtClientId"]) && !empty($_POST["txtCoordinates"]) && !empty($_POST["optNeighborhood"]) && !empty($_POST["optLocationType"]))) {

                    $clie_id = filter_input(INPUT_POST, "txtClientId", FILTER_SANITIZE_NUMBER_INT);
                    $coodinates = filter_input(INPUT_POST, "txtCoordinates");
                    $neighborhood = filter_input(INPUT_POST, "optNeighborhood");
                    $locationType = filter_input(INPUT_POST, "optLocationType");

                    if (!$clie_id || !$coodinates || !$neighborhood || !$locationType) {
                        $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                        exit;
                    }

                    // Check if client exists
                    if (!$client = $clientDAO->getClient($clie_id)) {
                        $message->setMessage("Cliente não encontrado", "danger", "back");
                        exit;
                    }

                    $coodinates = trim($coodinates);
                    $neighborhood = trim($neighborhood);

                    // Validating the provided coordinates.
                    $splitCoordinates = explode(",", $coodinates);

                    if (count($splitCoordinates) == 2) {

                        $latitude = trim($splitCoordinates[0]);
                        $longitude = trim($splitCoordinates[1]);
                    } elseif (count($splitCoordinates) == 4) {

                        $latitude = trim($splitCoordinates[0] . "." . $splitCoordinates[1]);
                        $longitude = trim($splitCoordinates[2] . "." . $splitCoordinates[3]);
                    } else {

                        $message->setMessage("Erro ao validar coordendas, verifique.", "danger", "back");
                        exit;
                    }

                    $location = new Location($client["id"]);
                    $location->setCoordinates($latitude, $longitude);
                    $location->setLocationNeighborhood($neighborhood);
                    $location->setLocationType($locationType);

                    // Add observations notes if provided
                    if (isset($_POST["txtObs"]) && !empty($_POST["txtObs"])) {
                        $obs = htmlspecialchars($_POST["txtObs"]);

                        $location->setLocationObservation($obs);
                    }

                    // Check if image was uploaded
                    if (isset($_FILES["imgHousePic"]) && $_FILES["imgHousePic"]["error"] == 0) {

                        //Check file type
                        if ($_FILES["imgHousePic"]["type"] == "image/png" || $_FILES["imgHousePic"]["type"] == "image/jpeg" || $_FILES["imgHousePic"]["type"] == "image/wepb") {

                            // Check file size
                            if ($_FILES["imgHousePic"]["size"] <= 5242880) {

                                switch ($_FILES["imgHousePic"]["type"]) {
                                    case "image/png":
                                        $fileExtension = ".png";
                                        break;

                                    case "image/jpeg":
                                        $fileExtension = ".jpg";
                                        break;

                                    case "image/webp":
                                        $fileExtension = ".webp";
                                        break;
                                }

                                // Generate random name
                                $fileName = bin2hex(random_bytes(16));

                                // Save to image's folders
                                if (move_uploaded_file($_FILES["imgHousePic"]["tmp_name"], "/var/www/html/imgs/houses/" . $fileName . $fileExtension)) {

                                    $location->setHousePicture($fileName . $fileExtension);
                                }
                            } else {
                                $message->setMessage("A imagem excedeu o tamanho máximo permitido de 5 megabytes", "danger", "back");
                                exit;
                            }
                        } else {
                            $message->setMessage("Tipo de imagem não permitido  ", "danger", "back");
                            exit;
                        }
                    }

                    // Check image uploaded from mobile version
                    if (isset($_POST["capturedPhoto"]) && !empty($_POST["capturedPhoto"])) {
                        $mobilePhoto = $_POST["capturedPhoto"];
                        $photoData = explode(",", $mobilePhoto);

                        // Check if uploaded image is a image/webp before decode
                        if ($photoData[0] === "data:image/webp;base64") {
                            $photoBase64 = base64_decode(end($photoData));
                        } else {
                            $message->setMessage("Formato de imagem inválido. Apenas WEBP é permitido.", "danger", "back");
                            exit;
                        }

                        // Check if decoded file is a valid image/webp
                        $webpInfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $webpInfo->buffer($photoBase64);

                        if ($mimeType === "image/webp") {
                            // Generate random name
                            $fileName = bin2hex(random_bytes(16));
                            file_put_contents("/var/www/html/imgs/houses/" . $fileName . ".webp", $photoBase64);

                            $location->setHousePicture($fileName . ".webp");
                        } else {
                            $message->setMessage("A imagem enviada não é um formado WEBP válido.", "danger", "back");
                            exit;
                        }
                    }

                    try {

                        $locationDAO->newLocation($location);
                        if (str_contains(dirname($_SERVER["HTTP_REFERER"]), "/delivery")) {
                            $message->setMessage("Localização salva com sucesso", "success", "delivery/viewclient.php?clientid=" . $clie_id);
                        } else {
                            $message->setMessage("Localização salva com sucesso", "success", "location.php?clie=" . $clie_id);
                        }
                    } catch (Exception $error) {

                        $message->setMessage($error->getMessage(), "danger", "back");
                    }
                } else {
                    $message->setMessage("Faltam campos obrigatórios!", "danger", "back");
                }
                break;

            case "delete":

                if (isset($_POST["locationId"]) && !empty($_POST["locationId"])) {
                    $delLocation = new LocationDAO($dbConn);

                    $loc = $delLocation->getLocation($_POST["locationId"]);

                    try {
                        $delLocation->deleteLocation($_POST["locationId"]);
                        $message->setMessage("Localização excluída com sucesso!", "success", "location.php");

                        // Delete image file if existis
                        if ($loc["housepicture"] != null) {
                            unlink("/var/www/html/imgs/houses/" . $loc["housepicture"]);
                        }
                    } catch (Exception $error) {
                        $message->setMessage($error->getMessage(), "danger", "location.php");
                    }
                }
                break;

            case "update":

                if ((isset($_POST["txtLocationId"]) && isset($_POST["txtCoordinates"]) && isset($_POST["optNeighborhood"]) && isset($_POST["optLocationType"])) && (!empty($_POST["txtLocationId"]) && !empty($_POST["txtCoordinates"]) && !empty($_POST["optNeighborhood"]) && !empty($_POST["optLocationType"]))) {

                    $loc_id = filter_input(INPUT_POST, "txtLocationId", FILTER_SANITIZE_NUMBER_INT);
                    $coodinates = filter_input(INPUT_POST, "txtCoordinates");
                    $neighborhood = filter_input(INPUT_POST, "optNeighborhood");
                    $locationType = filter_input(INPUT_POST, "optLocationType");

                    if (!$loc_id || !$coodinates || !$neighborhood || !$locationType) {
                        $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                        exit;
                    }

                    // Check if location exists
                    if (!$getlocation = $locationDAO->getLocation($loc_id)) {
                        $message->setMessage("Localização não encontrada", "danger", "back");
                        exit;
                    }

                    $coodinates = trim($coodinates);
                    $neighborhood = trim($neighborhood);

                    // Validating the provided coordinates.
                    $splitCoordinates = explode(",", $coodinates);

                    if (count($splitCoordinates) == 2) {

                        $latitude = trim($splitCoordinates[0]);
                        $longitude = trim($splitCoordinates[1]);
                    } elseif (count($splitCoordinates) == 4) {

                        $latitude = trim($splitCoordinates[0] . "." . $splitCoordinates[1]);
                        $longitude = trim($splitCoordinates[2] . "." . $splitCoordinates[3]);
                    } else {

                        $message->setMessage("Erro ao validar coordendas, verifique.", "danger", "back");
                        exit;
                    }

                    $location = new Location($getlocation["clientid"]);
                    $location->setLocationId($getlocation["id"]);
                    $location->setCoordinates($latitude, $longitude);
                    $location->setLocationNeighborhood($neighborhood);
                    $location->setLocationType($locationType);

                    // Add observations notes if provided
                    if (isset($_POST["txtObs"]) && !empty($_POST["txtObs"])) {
                        $obs = htmlspecialchars($_POST["txtObs"]);

                        $location->setLocationObservation($obs);
                    }

                    // Check if image was uploaded
                    if (isset($_FILES["imgHousePic"]) && $_FILES["imgHousePic"]["error"] == 0) {

                        //Check file type
                        if ($_FILES["imgHousePic"]["type"] == "image/png" || $_FILES["imgHousePic"]["type"] == "image/jpeg") {

                            // Check file size
                            if ($_FILES["imgHousePic"]["size"] <= 5242880) {

                                switch ($_FILES["imgHousePic"]["type"]) {
                                    case "image/png":
                                        $fileExtension = ".png";
                                        break;

                                    case "image/jpeg":
                                        $fileExtension = ".jpg";
                                        break;
                                }

                                // Generate random name
                                $fileName = bin2hex(random_bytes(16));

                                // Save to image's folders
                                if (move_uploaded_file($_FILES["imgHousePic"]["tmp_name"], "/var/www/html/imgs/houses/" . $fileName . $fileExtension)) {

                                    $location->setHousePicture($fileName . $fileExtension);

                                    // Delete old image if exists
                                    if (!empty($getlocation["housepicture"])) {
                                        unlink("/var/www/html/imgs/houses/" . $getlocation["housepicture"]);
                                    }
                                }
                            } else {
                                $message->setMessage("A imagem excedeu o tamanho máximo permitido de 5 megabytes", "danger", "back");
                                exit;
                            }
                        } else {
                            $message->setMessage("Tipo de imagem não permitido  ", "danger", "back");
                            exit;
                        }
                    } else {

                        if (!empty($getlocation["housepicture"])) {
                            $location->setHousePicture($getlocation["housepicture"]);
                        }
                    }

                    // Check image uploaded from mobile version
                    if (isset($_POST["capturedPhoto"]) && !empty($_POST["capturedPhoto"])) {
                        $mobilePhoto = $_POST["capturedPhoto"];
                        $photoData = explode(",", $mobilePhoto);
                        
                        // Check if uploaded image is a image/webp before decode
                        if ($photoData[0] === "data:image/webp;base64") {
                            $photoBase64 = base64_decode(end($photoData));
                        } else {
                            $message->setMessage("Formato de imagem inválido. Apenas WEBP é permitido.", "danger", "back");
                            exit;
                        }

                        // Check if decoded file is a valid image/webp
                        $webpInfo = new finfo(FILEINFO_MIME_TYPE);
                        $mimeType = $webpInfo->buffer($photoBase64);

                        if ($mimeType === "image/webp") {
                            // Generate random name
                            $fileName = bin2hex(random_bytes(16));
                            file_put_contents("/var/www/html/imgs/houses/" . $fileName . ".webp", $photoBase64);

                            $location->setHousePicture($fileName . ".webp");

                            // Delete old image if exists
                            if (!empty($getlocation["housepicture"])) {
                                unlink("/var/www/html/imgs/houses/" . $getlocation["housepicture"]);
                            }
                        } else {
                            $message->setMessage("A imagem enviada não é um formado WEBP válido.", "danger", "back");
                            exit;
                        }
                    }

                    try {

                        $locationDAO->updateLocation($location);

                        if (str_contains(dirname($_SERVER["HTTP_REFERER"]), "/delivery")) {
                            $message->setMessage("Localização alterada com sucesso", "success", "delivery/viewclient.php?clientid=" . $getlocation["clientid"]);
                        } else {
                            $message->setMessage("Localização alterada com sucesso", "success", "location.php?clie=" . $getlocation["clientid"]);
                        }
                    } catch (Exception $error) {

                        $message->setMessage($error->getMessage(), "danger", "back");
                    }
                } else {
                    $message->setMessage("Faltam campos obrigatórios!", "danger", "back");
                }
                break;

            default:
                # code...
                break;
        }
    }
} else {

    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(["status" => 401, "message" => "Method not allowed."]);
}
