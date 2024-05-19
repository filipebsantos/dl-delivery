<?php
    /*This class implements the Clients.*/
    
    class Client {

        private $id;
        private $name;

        public function __construct() {
            
        }

        public function setClientId(int $id) {
            $this->id = intval($id);
        }

        public function getClientId() {
            return $this->id;
        }

        public function setClientName(string $clientName) {
            $this->name = $clientName;
        }

        public function getClientName() {
            return $this->name;
        }
    }

    interface ClientInterface {
        // List all clients
        public function listClients();

        // Get specific client
        public function getClient(int $clientId);

        // Create new client
        public function newClient(Client $client);

        // Update client record
        public function updateClient(Client $client);

        // Delete client
        public function deleteClient(int $clientId);

        public function searchClient(string $criterion, string $search);
    }