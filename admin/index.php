<?php
  session_start();
  if(!isset($_SESSION['login_response']['success'])){
    header('location: http://localhost/web-app/cap-alx-01/backend/api/logout');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../css/admin-styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css">
</head>
<body>
  <?php include 'sidebar.php'?>
  <section class="home-section">
    <div class="text">Home</div>
  </section>
  <script src="../js/sidebar-effects.js"></script>
</body>
</html>