<?php
include '../config.php';
require 'escpos/autoload.php';
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;


function post_register(){
  $response = array();

  global $conn;

  $first_name = $_POST['first_name'] ?? '';
  $last_name = $_POST['last_name'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if(empty($first_name)){
    $response['error'] = 'First name is required.';
    echo json_encode($response);
    return;
  }

  if(empty($last_name)){
    $response['error'] = 'Last name is required.';
    echo json_encode($response);
    return;
  }

  if(empty($email)){
    $response['error'] = 'Email is required.';
    echo json_encode($response);
    return;
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $response['error'] = 'Invalid email format.';
    echo json_encode($response);
    return;
  }

  if(empty($password)){
    $response['error'] = 'Password is required.';
    echo json_encode($response);
    return;
  }

  if(empty($confirm_password)){
    $response['error'] = 'Confirm password is required.';
    echo json_encode($response);
    return;
  }

  if($password !== $confirm_password){
    $response['error'] = 'Passwords do not match.';
    echo json_encode($response);
    return;
  }

  // Hash the password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Check if email already exists
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if($result->num_rows > 0){
    $response['error'] = 'Email already exists.';
    echo json_encode($response);
    return;
  }

  // Insert user into the database
  $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

  if($stmt->execute()){
    $response['success'] = 'User registered successfully.';
  } else {
    $response['error'] = 'Error registering user: ' . $conn->error;
  }
  
  $stmt->close();
  echo json_encode($response);
}

function post_login(){
  $response = array();

  global $conn;

  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';

  if(empty($email)){
    $response['error'] = 'Email is required.';
    echo json_encode($response);
    return;
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $response['error'] = 'Invalid email format.';
    echo json_encode($response);
    return;
  }

  if(empty($password)){
    $response['error'] = 'Password is required.';
    echo json_encode($response);
    return;
  }

  // Retrieve user from database by email
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();

  if($result->num_rows == 0){
    $response['error'] = 'User not found.';
    echo json_encode($response);
    return;
  }

  $user = $result->fetch_assoc();

  // Verify password
  if(password_verify($password, $user['password'])){
    // Remove password hash from response for security
    unset($user['password']);
    $response['success'] = 'Login successful.';
    $response['message'] = True;
    $response['user'] = $user;

    // Store login response in session
    session_start();
    $_SESSION['login_response'] = array(
      'success' => 'Login successful.',
      'message' => true,
      'user' => $user
    );
  } else {
    $response['error'] = 'Invalid email or password.';
  }

  echo json_encode($response);
}

function post_add_stock(){
  $response = array();

  global $conn;

  $product_name = $_POST['product_name'] ?? '';
  $description = $_POST['description'] ?? '';
  $quantity = $_POST['quantity'] ?? '';
  $unit_price = $_POST['unit_price'] ?? '';
  $expiry_date = $_POST['expiry_date'] ?? '';

  if(empty($product_name)){
    $response['error'] = 'Product name is required.';
    echo json_encode($response);
    return;
  }

  if(empty($quantity)){
    $response['error'] = 'Quantity is required.';
    echo json_encode($response);
    return;
  } elseif(!is_numeric($quantity) || $quantity < 0){
    $response['error'] = 'Invalid quantity.';
    echo json_encode($response);
    return;
  }

  if(empty($unit_price)){
    $response['error'] = 'Unit price is required.';
    echo json_encode($response);
    return;
  } elseif(!is_numeric($unit_price) || $unit_price < 0){
    $response['error'] = 'Invalid unit price.';
    echo json_encode($response);
    return;
  }

  if(empty($expiry_date)){
    $response['error'] = 'Expiry date is required.';
    echo json_encode($response);
    return;
  }

  // Generate product ID
  $product_id = 'PR-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);;

  // Insert new stock entry into the database
  $stmt = $conn->prepare("INSERT INTO stocks (product_id, product_name, description, quantity, unit_price, expiry_date) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $product_id, $product_name, $description, $quantity, $unit_price, $expiry_date);

  if($stmt->execute()){
    $response['success'] = 'Stock added successfully.';
  } else {
    $response['error'] = 'Error adding stock: ' . $conn->error;
  }

  $stmt->close();

  echo json_encode($response);
}

function post_add_sell(){
  $response = array();

  global $conn;

  // Get the JSON data containing the products to sell from the request body
  $json_data = file_get_contents('php://input');
  $products = json_decode($json_data, true);

  // Check if JSON data is valid
  if(empty($products)){
    $response['error'] = 'Invalid JSON data.';
    echo json_encode($response);
    return;
  }

  // Generate a unique slip_no
  $slip_no = time() . mt_rand(100, 999);

  // Start a transaction
  $conn->begin_transaction();

  // Validate and sell each product
  foreach($products as $product){
    // Ensure both product_id and quantity are provided
    if(empty($product['product_id']) || empty($product['quantity'])){
      $response['error'] = 'Product ID and quantity are required for each item.';
      $conn->rollback();
      echo json_encode($response);
      return;
    }

    // Check if the requested quantity is available
    $stmt_check_quantity = $conn->prepare("SELECT quantity FROM stocks WHERE product_id = ?");
    $stmt_check_quantity->bind_param("s", $product['product_id']);
    $stmt_check_quantity->execute();
    $result = $stmt_check_quantity->get_result();
    $row = $result->fetch_assoc();
    $available_quantity = $row['quantity'];
    $stmt_check_quantity->close();

    if($available_quantity < $product['quantity']){
      // If the requested quantity is greater than available quantity, rollback the transaction and return an error
      $conn->rollback();
      $response['error'] = 'Not enough stock available for product with ID: ' . $product['product_id'];
      echo json_encode($response);
      return;
    }

    // Update the stock quantity
    $updated_quantity = $available_quantity - $product['quantity'];
    $stmt_update_quantity = $conn->prepare("UPDATE stocks SET quantity = ? WHERE product_id = ?");
    $stmt_update_quantity->bind_param("is", $updated_quantity, $product['product_id']);
    $stmt_update_quantity->execute();
    $stmt_update_quantity->close();

    // Insert the sell data with the generated slip_no
    $stmt_insert_sell = $conn->prepare("INSERT INTO sell (slip_no, product_id, quantity) VALUES (?, ?, ?)");
    $stmt_insert_sell->bind_param("isi", $slip_no, $product['product_id'], $product['quantity']);
    $stmt_insert_sell->execute();
    $stmt_insert_sell->close();
  }

  // Commit the transaction
  $conn->commit();

  $response['success'] = 'Sell data inserted successfully.';
  $response['slip_no'] = $slip_no; // Include slip_no in the response
  echo json_encode($response);
}




function dashboard(){
  $response = array();

  global $conn;

  // Get the current year and month
  $current_year = date('Y');
  $current_month = date('n');
  $current_day = date('j');

  // Prepare the SQL query to fetch monthly sales reports
  $query = "SELECT MONTHNAME(sell.sell_date) AS month, 
                   SUM(sell.quantity * stocks.unit_price) AS total_sales 
            FROM sell
            LEFT JOIN stocks ON sell.product_id = stocks.product_id
            WHERE YEAR(sell.sell_date) = ?
            GROUP BY YEAR(sell.sell_date), MONTH(sell.sell_date)
            ORDER BY YEAR(sell.sell_date), MONTH(sell.sell_date)";
  
  // Prepare and bind parameters for the SQL query
  $stmt = $conn->prepare($query);
  $stmt->bind_param("s", $current_year);

  // Execute the SQL query
  if($stmt->execute()){
    $result = $stmt->get_result();

    // Initialize an array to store monthly sales
    $monthly_sales_reports = array();

    // Loop through each month from January to December
    for ($month_counter = 1; $month_counter <= 12; $month_counter++) {
      $month_name = date('F', mktime(0, 0, 0, $month_counter, 1, $current_year));
      $found = false;

      // Check if there are sales for the current month in the fetched data
      while ($row = $result->fetch_assoc()) {
        if ($row['month'] === $month_name) {
          $monthly_sales_reports[] = $row;
          $found = true;
          break;
        }
      }

      // If no sales were found for the current month, add an entry with sales amount 0
      if (!$found) {
        $monthly_sales_reports[] = array('month' => $month_name, 'total_sales' => 0);
      }

      // Move the result pointer back to the beginning for the next iteration
      $result->data_seek(0);
    }

    // Check if any sales reports were found
    if(!empty($monthly_sales_reports)){
      $response['Sales'] = $monthly_sales_reports;
    } else {
      $response['error'] = 'No sales reports found for the current year.';
    }
  } else {
    // Error executing the SQL query
    $response['error'] = 'Error fetching monthly sales reports: ' . $conn->error;
  }

  // Close the statement
  $stmt->close();

  // Calculate total sales for last month, this month, and today
  $last_month_start = date('Y-m-d', strtotime('first day of last month'));
  $last_month_end = date('Y-m-d', strtotime('last day of last month'));
  $this_month_start = date('Y-m-d', strtotime('first day of this month'));
  $this_month_end = date('Y-m-d');
  $today = date('Y-m-d');
  $total_sales_last_month = 0;
  $total_sales_this_month = 0;
  $total_sales_today = 0;
  
  // Fetch total sales for last month
  $query_last_month = "SELECT SUM(sell.quantity * stocks.unit_price) AS total_sales_last_month 
                        FROM sell
                        LEFT JOIN stocks ON sell.product_id = stocks.product_id
                        WHERE DATE(sell.sell_date) BETWEEN ? AND ?";
  $stmt_last_month = $conn->prepare($query_last_month);
  if($stmt_last_month) {
    $stmt_last_month->bind_param("ss", $last_month_start, $last_month_end);
    $stmt_last_month->execute();
    $result_last_month = $stmt_last_month->get_result();
    $total_sales_last_month_row = $result_last_month->fetch_assoc();
    $total_sales_last_month = $total_sales_last_month_row['total_sales_last_month'] ?? 0;
    $stmt_last_month->close();
  } else {
    $response['error'] = 'Error preparing statement for last month\'s sales: ' . $conn->error;
  }

  // Fetch total sales for this month
  $query_this_month = "SELECT SUM(sell.quantity * stocks.unit_price) AS total_sales_this_month 
                        FROM sell
                        LEFT JOIN stocks ON sell.product_id = stocks.product_id
                        WHERE DATE(sell.sell_date) BETWEEN ? AND ?";
  $stmt_this_month = $conn->prepare($query_this_month);
  if($stmt_this_month) {
    $stmt_this_month->bind_param("ss", $this_month_start, $this_month_end);
    $stmt_this_month->execute();
    $result_this_month = $stmt_this_month->get_result();
    $total_sales_this_month_row = $result_this_month->fetch_assoc();
    $total_sales_this_month = $total_sales_this_month_row['total_sales_this_month'] ?? 0;
    $stmt_this_month->close();
  } else {
    $response['error'] = 'Error preparing statement for this month\'s sales: ' . $conn->error;
  }

  // Fetch total sales for today
  $query_today = "SELECT SUM(sell.quantity * stocks.unit_price) AS total_sales_today 
                  FROM sell
                  LEFT JOIN stocks ON sell.product_id = stocks.product_id
                  WHERE DATE(sell.sell_date) = ?";
  $stmt_today = $conn->prepare($query_today);
  if($stmt_today) {
    $stmt_today->bind_param("s", $today);
    $stmt_today->execute();
    $result_today = $stmt_today->get_result();
    $total_sales_today_row = $result_today->fetch_assoc();
    $total_sales_today = $total_sales_today_row['total_sales_today'] ?? 0;
    $stmt_today->close();
  } else {
    $response['error'] = 'Error preparing statement for today\'s sales: ' . $conn->error;
  }

  // Check if last month or this month had better sales
  $last_month_better = $total_sales_last_month > $total_sales_this_month;
  $this_month_better = $total_sales_this_month > $total_sales_last_month;

  // Add total sales for last month, this month, and today to the response
  $response['Monthly'] = array(
    'sales_last_month' => $total_sales_last_month,
    'sales_this_month' => $total_sales_this_month,
    'last_month_better' => $last_month_better,
    'this_month_better' => $this_month_better
  );

  // Add total sales for today to the response
  $response['Daily'] = array(
    'sales_today' => $total_sales_today
  );

  // Fetch overall stocks along with product names and quantities
  $query_stocks = "SELECT stocks.product_name, SUM(stocks.quantity) AS total_quantity 
                    FROM stocks 
                    GROUP BY stocks.product_name LIMIT 6";
  $result_stocks = $conn->query($query_stocks);

  // Initialize an array to store stock details
  $stocks = array();
  $total_stock_quantity = 0;

  if ($result_stocks) {
    // Fetch stock details
    while ($row_stocks = $result_stocks->fetch_assoc()) {
      $stocks[] = $row_stocks;
      $total_stock_quantity += $row_stocks['total_quantity'];
    }
    $response['Stocks'] = $stocks;
    $response['TotalStockQuantity'] = $total_stock_quantity;
  } else {
    $response['error'] = 'Error fetching stock details: ' . $conn->error;
  }

  // Fetch last three orders with product details
  $query_orders = "SELECT sell.id, sell.slip_no, sell.product_id, sell.quantity, sell.sell_date, 
    stocks.product_name, stocks.unit_price
    FROM sell
    LEFT JOIN stocks ON sell.product_id = stocks.product_id
    ORDER BY sell.sell_date DESC
    LIMIT 3";
  $result_orders = $conn->query($query_orders);

  // Initialize an array to store order details
  $orders = array();

  if ($result_orders) {
    // Fetch order details
    while ($row_orders = $result_orders->fetch_assoc()) {
      // Calculate the total amount for each order
      $total_amount = $row_orders['quantity'] * $row_orders['unit_price'];

      // Create an array to store order details
      $order_details = array(
      'id' => $row_orders['id'],
      'slip_no' => $row_orders['slip_no'],
      'product_id' => $row_orders['product_id'],
      'product_name' => $row_orders['product_name'],
      'quantity' => $row_orders['quantity'],
      'unit_price' => $row_orders['unit_price'],
      'total_amount' => $total_amount,
      'sell_date' => $row_orders['sell_date']
    );

    // Add order details to the orders array
    $orders[] = $order_details;
    }
    $response['Orders'] = $orders;
  } else {
    $response['error'] = 'Error fetching order details: ' . $conn->error;
  }

  // Return the response as JSON
  echo json_encode($response);
}

function get_all_stocks(){
  $response = array();

  global $conn;

  // Retrieve all stocks from the database
  $result = $conn->query("SELECT * FROM stocks ORDER BY product_name ASC");

  if($result){
    if($result->num_rows > 0){
      $stocks = array();
      while($row = $result->fetch_assoc()){
        $stocks[] = $row;
      }
      $response = $stocks;
    } else {
      $response['error'] = 'No stocks found.';
    }
  } else {
    $response['error'] = 'Error retrieving stocks: ' . $conn->error;
  }

  echo json_encode($response);
}

function get_sales_report(){
  $response = array();

  global $conn;

  // Get start date and end date from $_GET parameters
  $start_date = $_GET['start_date'] ?? '';
  $end_date = $_GET['end_date'] ?? '';

  // Check if start date and end date are provided
  if(empty($start_date) || empty($end_date)){
    $response['error'] = 'Start date and end date are required.';
    echo json_encode($response);
    return;
  }

  // Prepare the SQL query to fetch sales reports between start date and end date
  $query = "SELECT sell.*, stocks.product_name, stocks.unit_price * sell.quantity AS total_unit_price FROM sell
            LEFT JOIN stocks ON sell.product_id = stocks.product_id
            WHERE DATE(sell.sell_date) BETWEEN ? AND ?
            ORDER BY sell.id";
  
  // Prepare and bind parameters for the SQL query
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $start_date, $end_date);

  // Execute the SQL query
  if($stmt->execute()){
    $result = $stmt->get_result();

    // Fetch the sales reports
    $sales_reports = array();
    while ($row = $result->fetch_assoc()) {
      $sales_reports[] = $row;
    }

    // Check if any sales reports were found
    if(!empty($sales_reports)){
      $response = $sales_reports;
    } else {
      $response['error'] = 'No sales reports found between the specified dates.';
    }
  } else {
    // Error executing the SQL query
    $response['error'] = 'Error fetching sales reports: ' . $conn->error;
  }

  // Close the statement
  $stmt->close();

  // Return the response as JSON
  echo json_encode($response);
}

function get_stock_report(){
  $response = array();

  global $conn;

  // Get start date and end date from $_GET parameters
  $start_date = $_GET['start_date'] ?? '';
  $end_date = $_GET['end_date'] ?? '';

  // Check if start date and end date are provided
  if(empty($start_date) || empty($end_date)){
    $response['error'] = 'Start date and end date are required.';
    echo json_encode($response);
    return;
  }

  // Prepare the SQL query to fetch current stocks available between start date and end date
  $query = "SELECT * FROM stocks WHERE DATE(stocks_date) BETWEEN ? AND ? ORDER BY id";
  
  // Prepare and bind parameters for the SQL query
  $stmt = $conn->prepare($query);
  $stmt->bind_param("ss", $start_date, $end_date);

  // Execute the SQL query
  if($stmt->execute()){
    $result = $stmt->get_result();

    // Fetch the current stocks
    $current_stocks = array();
    while ($row = $result->fetch_assoc()) {
      $current_stocks[] = $row;
    }

    // Check if any current stocks were found
    if(!empty($current_stocks)){
      $response = $current_stocks;
    } else {
      $response['error'] = 'No current stocks found between the specified dates.';
    }
  } else {
    // Error executing the SQL query
    $response['error'] = 'Error fetching current stocks: ' . $conn->error;
  }

  // Close the statement
  $stmt->close();

  // Return the response as JSON
  echo json_encode($response);
}

function get_auth(){
  session_start();
  if(isset($_SESSION['login_response'])){
    echo json_encode($_SESSION['login_response']);
  } else {
    header('Location: http://localhost/web-app/cap-alx-01/backend/api/logout');
    exit();
  }
}

function get_logout(){
  session_start();
  $_SESSION = array();
  session_destroy();
  // Send JSON response indicating successful logout
  $response = array(
    'success' => true,
    'message' => 'Logout successful',
  );
  echo json_encode($response);
  header('location: http://localhost/web-app/cap-alx-01/');
  exit();
}

function print_sales() {
  global $conn;
  session_start();

  // Check if user session data is available
  if (!isset($_SESSION['login_response']['user'])) {
    $printer->text("Error: User session data not found. Please log in.\n");
    return;
  }

  $printerName = 'CAPALX';

  $connector = new WindowsPrintConnector($printerName);

  $printer = new Printer($connector);

  // Fetch slip_no from the URL
  if (isset($_GET['slip_no'])) {
      $slip_no = $_GET['slip_no'];
  } else {
      echo "Error: slip_no parameter not provided in the URL";
      return;
  }

  try {
      // Fetch data from the sell table using the slip_no
      // Example query:
      $sql = "SELECT sell.product_id, sell.quantity AS sold_quantity, sell.sell_date,
                     stocks.product_name, stocks.unit_price, stocks.quantity AS initial_quantity, stocks.stocks_date
              FROM sell
              JOIN stocks ON sell.product_id = stocks.product_id
              WHERE sell.slip_no = ?";
      $stmt = $conn->prepare($sql);
      if (!$stmt) {
          throw new Exception("Error preparing SQL statement: " . $conn->error);
      }
      $stmt->bind_param('s', $slip_no);
      $result = $stmt->execute();
      if (!$result) {
          throw new Exception("Error executing SQL statement: " . $stmt->error);
      }
      $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  } catch (Exception $e) {
      echo "Error fetching data from database: " . $e->getMessage();
      return;
  }

  // Print header
  $printer->text("BINAKI SELLER\n");
  $printer->text(str_repeat("-", 32) . "\n");
  $printer->text("Slip#: " . $slip_no . "\n");
  $user = $_SESSION['login_response']['user'];
  $printer->text("Staff: " . $user['first_name'] . " " . $user['last_name'] . "\n");
  $printer->text("Date: " . date("Y-m-d") . "\n");
  $printer->text(str_repeat("-", 32) . "\n");

  // Print table headers
  $printer->text("Qty  Description          Amount\n");
  $printer->text(str_repeat("-", 32) . "\n");

  // Print items
  foreach ($items as $item) {
      $qty = $item['sold_quantity'];
      $description = substr($item['product_name'], 0, 13); // Truncate description if necessary
      $amount = number_format($item['unit_price'], 2) . " Php";
      
      // Adjust spacing and alignment
      $printer->text(sprintf("%-5s %-15s %10s\n", $qty, $description, $amount));
  }

  // Print subtotal, tax, cash, and change
  $printer->text(str_repeat("-", 32) . "\n");
  $printer->text(sprintf("%-21s %s\n", "Subtotal:", str_pad(number_format($_GET['subtotal'], 2) . " Php", 10, ' ', STR_PAD_LEFT)));
  $printer->text(sprintf("%-21s %s\n", "Tax:", str_pad(number_format($_GET['tax'], 2) . " Php", 10, ' ', STR_PAD_LEFT)));
  $printer->text(sprintf("%-21s %s\n", "Cash:", str_pad($_GET['cash'] . " Php", 10, ' ', STR_PAD_LEFT)));
  $printer->text(sprintf("%-21s %s\n", "Change:", str_pad($_GET['change'] . " Php", 10, ' ', STR_PAD_LEFT)));
  $printer->text(str_repeat("-", 32) . "\n");

  // Print total
  $total = $_GET['subtotal'] + $_GET['tax'];
  $printer->text(sprintf("%-21s %s\n", "Total:", str_pad(number_format($total, 2) . " Php", 10, ' ', STR_PAD_LEFT)));
  $printer->text(str_repeat("-", 32) . "\n");

  // Print thank you message
  $printer->text("Thank you for your purchase!\n\n\n\n");

  // Close printer connection
  $printer->cut();
  $printer->close();
}

function print_sales_report() {
  global $conn;

  // Get start and end dates from the GET parameters
  $startDate = $_GET['start_date'];
  $endDate = $_GET['end_date'];

  // Prepare and execute SQL query to fetch data from the sell and stocks tables between the specified dates
  try {
      $sql = "SELECT sell.slip_no, stocks.product_name, sell.quantity, sell.sell_date, stocks.unit_price 
              FROM sell 
              INNER JOIN stocks ON sell.product_id = stocks.product_id 
              WHERE DATE(sell.sell_date) BETWEEN ? AND ? ORDER BY id DESC";
      $stmt = $conn->prepare($sql);
      if (!$stmt) {
          throw new Exception("Error preparing SQL statement: " . $conn->error);
      }
      $stmt->bind_param('ss', $startDate, $endDate);
      $result = $stmt->execute();
      if (!$result) {
          throw new Exception("Error executing SQL statement: " . $stmt->error);
      }
      $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  } catch (Exception $e) {
      echo "Error fetching data from database: " . $e->getMessage();
      return;
  }

  $totalSales = 0;

  // Print header
  $printerName = 'CAPALX';
  $connector = new WindowsPrintConnector($printerName);
  $printer = new Printer($connector);

  // Print header
  $printer->text("CAP ALEX INVENTORIES\n");
  $printer->text(str_repeat("-", 32) . "\n");
  $printer->text("Sales Report\n");
  $printer->text("Start Date: $startDate\n");
  $printer->text("End Date: $endDate\n");

  // Calculate total sales and print table headers
  $printer->text(str_repeat("-", 32) . "\n");
  $printer->text(sprintf("%-15s %5s %9s", "Product", "Qty", "Total\n")); // Changed "Quantity" to "Qty"
  $printer->text(str_repeat("-", 32) . "\n");

  // Print items
  foreach ($data as $item) {
      $productName = substr($item['product_name'], 0, 15); // Truncate product name if necessary
      $quantity = $item['quantity'];
      $totalUnitPrice = $quantity * $item['unit_price'];
      $printer->text(sprintf("%-15s %5s %9s\n", $productName, $quantity, "Php " . number_format($totalUnitPrice, 2)));
      $totalSales += $totalUnitPrice;
  }

  // Print total sales
  $printer->text(str_repeat("-", 32) . "\n");
  $printer->text("Total Sales: Php " . number_format($totalSales, 2) . "\n\n\n\n");

  // Close printer connection
  $printer->cut();
  $printer->close();
}

function print_stocks_report() {
  global $conn;

  // Prepare and execute SQL query to fetch data from the stocks table
  try {
      $sql = "SELECT product_name, quantity, unit_price 
              FROM stocks ORDER BY product_name";
      $result = $conn->query($sql);
      if (!$result) {
          throw new Exception("Error executing SQL statement: " . $conn->error);
      }
      $data = $result->fetch_all(MYSQLI_ASSOC);
  } catch (Exception $e) {
      echo "Error fetching data from database: " . $e->getMessage();
      return;
  }

  // Print header
  $printerName = 'CAPALX';
  $connector = new WindowsPrintConnector($printerName);
  $printer = new Printer($connector);

  // Print header
  $printer->text("CAP ALEX INVENTORIES\n");
  $printer->text(str_repeat("-", 32) . "\n");
  $printer->text("Stock Report\n");

  // Print table headers
  $printer->text(str_repeat("-", 32) . "\n");
  $printer->text(sprintf("%-15s %5s %9s", "Product", "Qty", "Unit Price\n")); // Using "Qty" instead of "Quantity"
  $printer->text(str_repeat("-", 32) . "\n");

  // Print items
  foreach ($data as $item) {
      $productName = substr($item['product_name'], 0, 15); // Truncate product name if necessary
      $quantity = $item['quantity'];
      $unitPrice = $item['unit_price'];
      $printer->text(sprintf("%-15s %5s %9s\n", $productName, $quantity, "Php " . number_format($unitPrice, 2)));
  }
  $printer->text("\n\n\n");

  // Close printer connection
  $printer->cut();
  $printer->close();
}





function put_update_stock(){
  $response = array();

  global $conn;

  $id = $_GET['product_id'] ?? '';
  $product_name = $_POST['product_name'] ?? '';
  $description = $_POST['description'] ?? '';
  $quantity = $_POST['quantity'] ?? '';
  $unit_price = $_POST['unit_price'] ?? '';
  $expiry_date = $_POST['expiry_date'] ?? '';

  if(empty($id)){
    $response['error'] = 'Product ID is required.';
    echo json_encode($response);
    return;
  }

  // Construct the SQL UPDATE statement
  $update_query = "UPDATE stocks SET ";

  $update_fields = array();
  if(!empty($product_name)){
    $update_fields[] = "product_name = '$product_name'";
  }
  if(!empty($description)){
    $update_fields[] = "description = '$description'";
  }
  if(!empty($quantity)){
    $update_fields[] = "quantity = $quantity";
  }
  if(!empty($unit_price)){
    $update_fields[] = "unit_price = $unit_price";
  }
  if(!empty($expiry_date)){
    $update_fields[] = "expiry_date = '$expiry_date'";
  }

  $update_query .= implode(", ", $update_fields) . " WHERE product_id = ?";

  // Prepare and execute the SQL UPDATE statement
  $stmt = $conn->prepare($update_query);
  $stmt->bind_param("s", $id); // Assuming product_id is a string, change to "i" if it's an integer

  if($stmt->execute()){
    if($stmt->affected_rows > 0){
      $response['success'] = 'Stock updated successfully.';
    } else {
      $response['error'] = 'No rows updated. Product ID not found.';
    }
  } else {
    $response['error'] = 'Error updating stock: ' . $conn->error;
  }

  $stmt->close();

  echo json_encode($response);
}




function delete_stock(){
  $response = array();

  global $conn;

  $product_id = $_GET['product_id'] ?? '';

  if(empty($product_id)){
    $response['error'] = 'Product ID is required.';
    echo json_encode($response);
    return;
  }

  // Delete the stock entry from the database
  $stmt = $conn->prepare("DELETE FROM stocks WHERE product_id = ?");
  $stmt->bind_param("s", $product_id);

  if($stmt->execute()){
    if($stmt->affected_rows > 0){
      $response['success'] = 'Stock deleted successfully.';
    } else {
      $response['error'] = 'No stock found with the given product ID.';
    }
  } else {
    $response['error'] = 'Error deleting stock: ' . $conn->error;
  }

  $stmt->close();

  echo json_encode($response);
}
?>
