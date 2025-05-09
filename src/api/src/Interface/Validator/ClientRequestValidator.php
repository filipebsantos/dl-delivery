<?php

namespace DLDelivery\Interface\Validator;

use DLDelivery\Application\DTO\Client\ClientDTO;
use DLDelivery\Application\DTO\Client\ClientFilterDTO;
use DLDelivery\Application\DTO\Client\LocationDTO;
use DLDelivery\Exception\Client\MissingCreateClientPayloadException;
use DLDelivery\Exception\InvalidJsonException;

class ClientRequestValidator
{
    public static function list(): ClientFilterDTO
    {
        $page = (int) (filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) ?: 1);
        $perPage = (int) (filter_input(INPUT_GET, 'perPage', FILTER_SANITIZE_NUMBER_INT) ?: 10);
        $filters = isset($_GET['filters']) ? (array) $_GET['filters'] : [];

        return new ClientFilterDTO(
            $page,
            $perPage,
            null,
            $filters
        );
    }

    public static function create(): ClientDTO
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$name || !$id) {
            throw new MissingCreateClientPayloadException;
        }

        return new ClientDTO($id, $name);
    }

    public static function update(int $id): ClientDTO
    {
        $rawData = json_decode(file_get_contents("php://input"), true);
        
        if (!is_array($rawData)) {
            throw new InvalidJsonException;
        }

        $name = filter_var($rawData['name'], FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$name || empty($name)) {
            throw new MissingCreateClientPayloadException;
        }

        return new ClientDTO($id, $name);
    }

    public static function createLocation(): LocationDTO
    {
        $latitude = isset($_POST['latitude']) ? filter_input(INPUT_POST, "latitude", FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $longitude = isset($_POST['longitude']) ? filter_input(INPUT_POST, "longitude", FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $neighborhoodID = isset($_POST['neighborhoodID']) ? filter_input(INPUT_POST, "neighborhoodID", FILTER_SANITIZE_NUMBER_INT) : null;

        if (is_null($latitude) || is_null($longitude) || is_null($neighborhoodID)) {

        }

        return new LocationDTO(
            $latitude,
            $longitude,
            $neighborhoodID,
            null,
            null
        );
    }
}