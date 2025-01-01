<?php
  session_start();
  if(!isset($_SESSION['login_response']['success'])){
    header('location: http://localhost/web-app/lspu-cmi/backend/api/logout');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stocks Management - Cap Alx 01</title>
  <link rel="stylesheet" href="../css/admin-styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css">
</head>
<body>
  <?php include 'sidebar.php'?>
  <section class="home-section">
    <div class="text">Stocks Management</div>

    <div class="search-container">
      <input type="text" class="search-input" id="search" placeholder="Search for products...">
    </div>
    <div class="table-container">
      <!-- Error Message Display -->
      <div id="error-message" class="error-message">hello</div>
      <!-- Success Message Display -->
      <div id="success-message" class="success-message" style="display: none;"></div>
      <table class="report-table" id="stocksTable">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Expiry Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Table body content will be dynamically populated here -->
        </tbody>
      </table>
    </div>
  </section>

  <script src="../js/sidebar-effects.js"></script>
  <script src="../js/stocks-management-functions.js"></script>
</body>
</html>
