<?php
// controllers/CountryController.php

require_once __DIR__ . '/../models/Country.php';
require_once __DIR__ . '/../utils/Response.php';

class CountryController {
    private $country;

    public function __construct($db) {
        $this->country = new Country($db);
    }

    // GET /countries - List all countries
    public function index() {
        $countries = $this->country->readAll();
        Response::success($countries);
    }

    // GET /countries/{id} - Show a single country
    public function show($id) {
        if (!is_numeric($id)) {
            Response::badRequest('Invalid ID');
        }

        $country = $this->country->readOne($id);
        
        if (!$country) {
            Response::notFound('Country not found');
        }

        Response::success($country);
    }

    // POST /countries - Create a new country
    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validation
        if (!isset($data['name']) || empty(trim($data['name']))) {
            Response::badRequest('The "name" field is required');
        }

        $name = trim($data['name']);

        // Length validation
        if (strlen($name) > 100) {
            Response::badRequest('Country name cannot exceed 100 characters');
        }

        $result = $this->country->create($name);

        if ($result === 'duplicate') {
            Response::error('A country with this name already exists', 409);
        }

        if (!$result) {
            Response::error('Error creating country', 500);
        }

        Response::created($result, 'Country created successfully');
    }

    // PUT /countries/{id} - Update a country
    public function update($id) {
        if (!is_numeric($id)) {
            Response::badRequest('Invalid ID');
        }

        // Check if country exists
        if (!$this->country->exists($id)) {
            Response::notFound('Country not found');
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Validation
        if (!isset($data['name']) || empty(trim($data['name']))) {
            Response::badRequest('The "name" field is required');
        }

        $name = trim($data['name']);

        // Length validation
        if (strlen($name) > 100) {
            Response::badRequest('Country name cannot exceed 100 characters');
        }

        $result = $this->country->update($id, $name);

        if ($result === 'duplicate') {
            Response::error('A country with this name already exists', 409);
        }

        if (!$result) {
            Response::error('No changes made', 400);
        }

        Response::success($result, 'Country updated successfully');
    }

    // DELETE /countries/{id} - Delete a country
    public function destroy($id) {
        if (!is_numeric($id)) {
            Response::badRequest('Invalid ID');
        }

        if (!$this->country->delete($id)) {
            Response::notFound('Country not found or already deleted');
        }

        Response::success(null, 'Country deleted successfully');
    }
}