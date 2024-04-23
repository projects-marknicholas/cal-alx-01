<?php
include '../config.php';

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

  // Generate product ID
  $product_id = 'PR-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);;

  // Insert new stock entry into the database
  $stmt = $conn->prepare("INSERT INTO stocks (product_id, product_name, description, quantity, unit_price) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $product_id, $product_name, $description, $quantity, $unit_price);

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





function put_update_stock(){
  $response = array();

  global $conn;

  $id = $_GET['product_id'] ?? '';
  $product_name = $_POST['product_name'] ?? '';
  $description = $_POST['description'] ?? '';
  $quantity = $_POST['quantity'] ?? '';
  $unit_price = $_POST['unit_price'] ?? '';

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
