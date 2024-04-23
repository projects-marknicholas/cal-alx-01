<?php

include 'api.php';

// Define your routes here
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Trim any query string from the URI
$path = strtok($path, '?');

// Define your routes
$routes = [
  // POST Requests
  'POST:/web-app/cap-alx-01/backend/api/register' => 'post_register',
  'POST:/web-app/cap-alx-01/backend/api/login' => 'post_login',
  'POST:/web-app/cap-alx-01/backend/api/add-stock' => 'post_add_stock',
  'POST:/web-app/cap-alx-01/backend/api/update-stock' => 'put_update_stock',
  'POST:/web-app/cap-alx-01/backend/api/add-sell' => 'post_add_sell',

  // GET Requests
  'GET:/web-app/cap-alx-01/backend/api/stocks' => 'get_all_stocks',
  'GET:/web-app/cap-alx-01/backend/api/stocks-report' => 'get_stock_report',
  'GET:/web-app/cap-alx-01/backend/api/sales-report' => 'get_sales_report',
  'GET:/web-app/cap-alx-01/backend/api/auth' => 'get_auth',
  'GET:/web-app/cap-alx-01/backend/api/logout' => 'get_logout',

  // PUT Requests
  
  // DELETE Requests
  'DELETE:/web-app/cap-alx-01/backend/api/delete-stock' => 'delete_stock',
];

// Match the current request to a route
$routeKey = "$method:$path";

if (array_key_exists($routeKey, $routes)) {
  $handler = $routes[$routeKey];
  if (function_exists($handler)) {
    call_user_func($handler);
  } else {
    http_response_code(404);
    echo "404 Not Found";
  }
} else {
  // Route not found
  http_response_code(404);
  echo "404 Not Found";
}
?>
