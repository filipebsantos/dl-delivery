<?php
    /*This class implements the Users.*/

    class Route {

        private $routeId;
        private $delieryman;
        private $createByUser;
        private $startTime;
        private $endTime;
        private $status;

        public function __construct() {
            
        }

        public function getRouteId() {
            return $this->routeId;
        }

        function setRouteId(int $routeid) {
            $this->routeId = intval($routeid);
        }

        public function getDeliveryman() {
            return $this->delieryman;
        }

        public function setDeliveryman(int $userid) {
            $this->delieryman = intval($userid);
        }

        public function getCreateByUSer() {
            return $this->createByUser;
        }

        public function setCreateByUser(int $userid) {
            $this->createByUser = intval($userid);
        }

        public function getStartTime() {
            return $this->startTime;
        } 

        public function setStartTime(DateTime $datetime) {
            $this->startTime = $datetime;
        }

        public function getEndTime() {
            return $this->endTime;
        }

        public function setEndTime(DateTime $datetime) {
            $this->endTime = $datetime;
        }

        public function getRouteStatus() {
            return $this->status;
        }

        public function setRouteStatus(string $status) {
            $this->status = $status;
        }
    }

    interface RouteInterface {
        // Add new route
        public function addNewRoute(Route $route);

        // Get route list
        public function listRoutes(string $date = null, bool $onlyUnfinished = true, int $deliveryman = 0);

        // Get route
        public function getRoute(int $routeid);

        // Get route locations
        public function getRouteLocations(int $routeid);

        // Add location to routes
        public function addRouteLocation(int $routeid, int $locationid);

        // Delete route
        public function deleteRoute(int $routeid);

        // Delete location from route
        public function deleteRouteLocation(int $routeid, int $locationid);

        // Change route status
        public function changeRouteStatus(int $routeid, string $status);

        // Add client to route
        public function addClientToRoute(int $routeid, int $clientid);

        // List route's clients
        public function listRouteClients(int $routeid);

        // Delete client from route
        public function deleteRouteClient(int $routeid, int $clientid);
    }