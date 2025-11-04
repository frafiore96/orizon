<?php
// models/Trip.php

class Trip {
    private $conn;
    private $table = 'trips';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a trip
    public function create($countryIds, $availableSeats) {
        try {
            $this->conn->beginTransaction();
            
            // Insert the trip
            $query = "INSERT INTO " . $this->table . " (available_seats) VALUES (:available_seats)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':available_seats', $availableSeats);
            $stmt->execute();
            
            $tripId = $this->conn->lastInsertId();
            
            // Associate countries with the trip
            if (!empty($countryIds)) {
                $this->associateCountries($tripId, $countryIds);
            }
            
            $this->conn->commit();
            
            return $this->readOne($tripId);
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Associate countries with a trip
    private function associateCountries($tripId, $countryIds) {
        $query = "INSERT INTO trip_countries (trip_id, country_id) VALUES (:trip_id, :country_id)";
        $stmt = $this->conn->prepare($query);
        
        foreach ($countryIds as $countryId) {
            $stmt->bindParam(':trip_id', $tripId);
            $stmt->bindParam(':country_id', $countryId);
            $stmt->execute();
        }
    }

    // Read a single trip
    public function readOne($id) {
        $query = "SELECT t.id, t.available_seats, t.created_at, t.updated_at
                  FROM " . $this->table . " t
                  WHERE t.id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $trip = $stmt->fetch();
        
        if ($trip) {
            $trip['countries'] = $this->getCountriesForTrip($id);
        }
        
        return $trip;
    }

    // Read countries associated with a trip
    private function getCountriesForTrip($tripId) {
        $query = "SELECT c.id, c.name 
                  FROM countries c
                  INNER JOIN trip_countries tc ON c.id = tc.country_id
                  WHERE tc.trip_id = :trip_id
                  ORDER BY c.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':trip_id', $tripId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Read all trips
    public function readAll($countryIds = null, $minSeats = null, $maxSeats = null) {
        $conditions = [];
        $params = [];
        
        $query = "SELECT DISTINCT t.id, t.available_seats, t.created_at, t.updated_at
                  FROM " . $this->table . " t";
        
        // Filter by countries
        if ($countryIds !== null && !empty($countryIds)) {
            $query .= " INNER JOIN trip_countries tc ON t.id = tc.trip_id";
            $placeholders = implode(',', array_fill(0, count($countryIds), '?'));
            $conditions[] = "tc.country_id IN ($placeholders)";
            $params = array_merge($params, $countryIds);
        }
        
        // Filter by available seats
        if ($minSeats !== null) {
            $conditions[] = "t.available_seats >= ?";
            $params[] = $minSeats;
        }
        
        if ($maxSeats !== null) {
            $conditions[] = "t.available_seats <= ?";
            $params[] = $maxSeats;
        }
        
        // Add conditions
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $query .= " ORDER BY t.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        $trips = $stmt->fetchAll();
        
        // Add countries for each trip
        foreach ($trips as &$trip) {
            $trip['countries'] = $this->getCountriesForTrip($trip['id']);
        }
        
        return $trips;
    }

    // Update a trip
    public function update($id, $countryIds = null, $availableSeats = null) {
        try {
            $this->conn->beginTransaction();
            
            // Update available seats if provided
            if ($availableSeats !== null) {
                $query = "UPDATE " . $this->table . " 
                          SET available_seats = :available_seats 
                          WHERE id = :id";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':available_seats', $availableSeats);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }
            
            // Update countries if provided
            if ($countryIds !== null) {
                // Remove all existing associations
                $deleteQuery = "DELETE FROM trip_countries WHERE trip_id = :trip_id";
                $stmt = $this->conn->prepare($deleteQuery);
                $stmt->bindParam(':trip_id', $id);
                $stmt->execute();
                
                // Add new associations
                if (!empty($countryIds)) {
                    $this->associateCountries($id, $countryIds);
                }
            }
            
            $this->conn->commit();
            
            return $this->readOne($id);
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Delete a trip
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    // Verify if a trip exists
    public function exists($id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['count'] > 0;
    }
}
