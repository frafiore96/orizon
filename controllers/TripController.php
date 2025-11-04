<?php
// controllers/TripController.php

require_once __DIR__ . '/../models/Trip.php';
require_once __DIR__ . '/../models/Country.php';
require_once __DIR__ . '/../utils/Response.php';

class TripController {
    private $trip;
    private $country;

    public function __construct($db) {
        $this->trip = new Trip($db);
        $this->country = new Country($db);
    }

    // GET /trips
    public function index() {
        $countryIds = isset($_GET['countries']) ? $_GET['countries'] : null;
        $minSeats = isset($_GET['min_seats']) ? $_GET['min_seats'] : null;
        $maxSeats = isset($_GET['max_seats']) ? $_GET['max_seats'] : null;

        // Convert country_ids in array if string
        if ($countryIds !== null) {
            if (is_string($countryIds)) {
                $countryIds = explode(',', $countryIds);
            }
            $countryIds = array_map('intval', $countryIds);
            $countryIds = array_filter($countryIds, function($id) {
                return $id > 0;
            });
            
            if (empty($countryIds)) {
                $countryIds = null;
            }
        }

        // Seats validation
        if ($minSeats !== null && (!is_numeric($minSeats) || $minSeats < 0)) {
            Response::badRequest('Parameter min_seats must be a number >= 0');
        }

        if ($maxSeats !== null && (!is_numeric($maxSeats) || $maxSeats < 0)) {
            Response::badRequest('Parameter max_seats must be a number >= 0');
        }

        if ($minSeats !== null && $maxSeats !== null && $minSeats > $maxSeats) {
            Response::badRequest('min_seats can not be greater than max_seats');
        }

        $trips = $this->trip->readAll($countryIds, $minSeats, $maxSeats);
        Response::success($trips);
    }

    // GET /trips/{id} - Show single trip
    public function show($id) {
        if (!is_numeric($id)) {
            Response::badRequest('Not valid ID');
        }

        $trip = $this->trip->readOne($id);
        
        if (!$trip) {
            Response::notFound('Trip not found');
        }

        Response::success($trip);
    }

    // POST /trips - Create new trip
    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (!isset($data['available_seats'])) {
            Response::badRequest('Field "available_seats" is required');
        }

        if (!isset($data['country_ids']) || !is_array($data['country_ids']) || empty($data['country_ids'])) {
            Response::badRequest('Field "country_ids" is required and must be an array with at least one element');
        }

        $availableSeats = $data['available_seats'];
        $countryIds = array_map('intval', $data['country_ids']);

        // Validate available_seats
        if (!is_numeric($availableSeats) || $availableSeats < 0) {
            Response::badRequest('Available seats must be a number >= 0');
        }

        // Validate country_ids
        $countryIds = array_unique($countryIds);
        $countryIds = array_filter($countryIds, function($id) {
            return $id > 0;
        });

        if (empty($countryIds)) {
            Response::badRequest('Almost one country ID is required');
        }

        // Verify if all countries exist
        if (!$this->country->allExist($countryIds)) {
            Response::badRequest('One or more countries do not exist');
        }

        $result = $this->trip->create($countryIds, $availableSeats);

        if (!$result) {
            Response::error('Error creating trip', 500);
        }

        Response::created($result, 'Trip created successfully');
    }

    // PUT /trips/{id} - Update a trip
    public function update($id) {
        if (!is_numeric($id)) {
            Response::badRequest('ID not valid');
        }

        // Verify if trip exists
        if (!$this->trip->exists($id)) {
            Response::notFound('Trip not found');
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $countryIds = isset($data['country_ids']) ? $data['country_ids'] : null;
        $availableSeats = isset($data['available_seats']) ? $data['available_seats'] : null;

        // Must be at least one field
        if ($countryIds === null && $availableSeats === null) {
            Response::badRequest('Fields "country_ids" and "available_seats" are required');
        }

        // Validate available_seats if provided
        if ($availableSeats !== null) {
            if (!is_numeric($availableSeats) || $availableSeats < 0) {
                Response::badRequest('Available seats must be a number >= 0');
            }
        }

        // Validate country_ids if provided
        if ($countryIds !== null) {
            if (!is_array($countryIds)) {
                Response::badRequest('country_ids must be an array');
            }

            if (empty($countryIds)) {
                Response::badRequest('country_ids can not be empty');
            }

            $countryIds = array_map('intval', $countryIds);
            $countryIds = array_unique($countryIds);
            $countryIds = array_filter($countryIds, function($id) {
                return $id > 0;
            });

            if (empty($countryIds)) {
                Response::badRequest('Almost one country ID is required');
            }

            // Verify if all countries exist
            if (!$this->country->allExist($countryIds)) {
                Response::badRequest('One or more countries do not exist');
            }
        }

        $result = $this->trip->update($id, $countryIds, $availableSeats);

        if (!$result) {
            Response::error('Error during trip update', 500);
        }

        Response::success($result, 'Trip updated successfully');
    }

    // DELETE /trips/{id} - Trip deleted
    public function destroy($id) {
        if (!is_numeric($id)) {
            Response::badRequest('ID not valid');
        }

        if (!$this->trip->delete($id)) {
            Response::notFound('Trip not found or already deleted');
        }

        Response::success(null, 'Trip deleted successfully');
    }
}
