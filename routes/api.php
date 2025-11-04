<?php

return [
    // countries routes
    'GET /countries' => ['controller' => 'CountryController', 'action' => 'index'],
    'GET /countries/{id}' => ['controller' => 'CountryController', 'action' => 'show'],
    'POST /countries' => ['controller' => 'CountryController', 'action' => 'store'],
    'PUT /countries/{id}' => ['controller' => 'CountryController', 'action' => 'update'],
    'DELETE /countries/{id}' => ['controller' => 'CountryController', 'action' => 'destroy'],
    
    // trips routes
    'GET /trips' => ['controller' => 'TripController', 'action' => 'index'],
    'GET /trips/{id}' => ['controller' => 'TripController', 'action' => 'show'],
    'POST /trips' => ['controller' => 'TripController', 'action' => 'store'],
    'PUT /trips/{id}' => ['controller' => 'TripController', 'action' => 'update'],
    'DELETE /trips/{id}' => ['controller' => 'TripController', 'action' => 'destroy'],
];
    