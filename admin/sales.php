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
  <title>Sales - Cap Alx 01</title>
  <link rel="stylesheet" href="../css/admin-styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css">
</head>
<body>
  <?php include 'sidebar.php'?>
  <section class="home-section">
    <div class="text">Sales</div>

    <div id="receipt"></div>

    <!-- Error Message Display -->
    <div id="error-message" class="error-message" style="display: none;">Error message goes here</div>
    <!-- Success Message Display -->
    <div id="success-message" class="success-message" style="display: none;">Success message goes here</div>

    <div class="all-products">
      <div class="left">
        <div class="grid-items" id="salesGrid">
          <!-- Stocks will be dynamically populated here -->
        </div>
      </div>
      <div class="right">
        <h1>Ordered Items</h1><br>
        <div id="selectedItems"></div>
        <div class="accounting-div">
          <div class="accounting">
            <div class="flex">
              <p>Cash</p>
              <input type="number" id="cashInput" class="quantity-input" min="0" step="0.01">
            </div>
            <div class="flex">
              <p>Change (Sukli)</p>
              <p id="change">0.00</p>
            </div>
            <div class="flex">
              <p>Subtotal</p>
              <p id="subtotal">0.00</p>
            </div>
            <div class="flex">
              <p>Tax</p>
              <p id="tax">0.00</p>
            </div>
            <div class="flex">
              <p>Total</p>
              <p id="total">0.00</p>
            </div>
          </div>
          <button id="placeOrderBtn">Place Order</button>
        </div>
      </div>
    </div>
  </section>

  <script src="../js/sidebar-effects.js"></script>
  <script src="../js/sales-functions.js"></script>
</body>
</html>
