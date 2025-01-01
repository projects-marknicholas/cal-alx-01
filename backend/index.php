<?php

// Security Headers
function cors() {
  if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
  }

  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
  }
}

cors();

include 'api.php';

// Define your routes here
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Trim any query string from the URI
$path = strtok($path, '?');

// Define your routes
$routes = [
  // POST Requests
  'POST:/web-app/lspu-cmi/backend/api/register' => 'post_register',
  'POST:/web-app/lspu-cmi/backend/api/login' => 'post_login',
  'POST:/web-app/lspu-cmi/backend/api/add-stock' => 'post_add_stock',
  'POST:/web-app/lspu-cmi/backend/api/update-stock' => 'put_update_stock',
  'POST:/web-app/lspu-cmi/backend/api/add-sell' => 'post_add_sell',

  // GET Requests
  'GET:/web-app/lspu-cmi/backend/api/dashboard' => 'dashboard',
  'GET:/web-app/lspu-cmi/backend/api/stocks' => 'get_all_stocks',
  'GET:/web-app/lspu-cmi/backend/api/stocks-report' => 'get_stock_report',
  'GET:/web-app/lspu-cmi/backend/api/sales-report' => 'get_sales_report',
  'GET:/web-app/lspu-cmi/backend/api/auth' => 'get_auth',
  'GET:/web-app/lspu-cmi/backend/api/logout' => 'get_logout',
  'GET:/web-app/lspu-cmi/backend/api/print-receipt' => 'print_sales',
  'GET:/web-app/lspu-cmi/backend/api/print-sales-report' => 'print_sales_report',
  'GET:/web-app/lspu-cmi/backend/api/print-stocks-report' => 'print_stocks_report',

  // PUT Requests
  
  // DELETE Requests
  'DELETE:/web-app/lspu-cmi/backend/api/delete-stock' => 'delete_stock',
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
