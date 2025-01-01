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
  <title>Sales Report - Cap Alx 01</title>
  <link rel="stylesheet" href="../css/admin-styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css">
</head>
<body>
  <?php include 'sidebar.php'?>
  <section class="home-section">
    <div class="text">Sales Report</div>

    <div class="search-container">
      <label for="start_date">Start (Date & Time)</label>
      <input type="date" class="search-input" id="start_date">
      <label for="end_date">End (Date & Time)</label>
      <input type="date" class="search-input" id="end_date">
      <button class="print-button">Print Sales Report</button>
    </div>

    <div class="table-container">
      <table class="report-table" id="pdfTable">
        <thead>
          <tr>
            <th>Product Name</th>
            <th>Unit</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <!-- Table body content will be dynamically populated here -->
        </tbody>
      </table>
    </div>
  </section>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
  <script src="../js/sidebar-effects.js"></script>
  <script src="../js/sales-report-functions.js"></script>
</body>
</html>