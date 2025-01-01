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
  <title>Add New Stocks - Cap Alx 01</title>
  <link rel="stylesheet" href="../css/admin-styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css">
</head>
<body>
  <?php include 'sidebar.php'?>
  <section class="home-section">
    <div class="text">Add New Stocks</div>

    <form>
      <!-- Error Message Display -->
      <div id="error-message" class="error-message">hello</div>
      <!-- Success Message Display -->
      <div id="success-message" class="success-message" style="display: none;">Stock added successfully!</div>

      <div>
        <label for="product_name">Product Name</label>
        <input type="text" id="product_name" name="product_name" required>
      </div>
      <div>
        <label for="description">Description</label>
        <input type="text" id="description" name="description">
      </div>
      <div>
        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" required>
      </div>
      <div>
        <label for="unit_price">Unit Price</label>
        <input type="number" id="unit_price" name="unit_price" step="0.01" required>
      </div>
      <div>
        <label for="expiry_date">Expiry Date</label>
        <input type="date" id="expiry_date" name="expiry_date" required>
      </div>
      <div>
        <button type="submit">Submit</button>
      </div>
    </form>
  </section>

  <script src="../js/sidebar-effects.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.querySelector('form');
      const errorMessage = document.getElementById('error-message');
      const successMessage = document.getElementById('success-message');

      form.addEventListener('submit', function (event) {
        event.preventDefault();

        const productName = document.getElementById('product_name').value;
        const description = document.getElementById('description').value;
        const quantity = document.getElementById('quantity').value;
        const unitPrice = document.getElementById('unit_price').value;
        const expiryDate = document.getElementById('expiry_date').value;

        // Create a FormData object to send form data
        const formData = new FormData();
        formData.append('product_name', productName);
        formData.append('description', description);
        formData.append('quantity', quantity);
        formData.append('unit_price', unitPrice);
        formData.append('expiry_date', expiryDate);

        // Send a POST request to the backend endpoint
        fetch('http://localhost/web-app/lspu-cmi/backend/api/add-stock', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            errorMessage.textContent = data.error;
            errorMessage.style.display = 'block';
            successMessage.style.display = 'none'; // Hide success message if there's an error
          } else {
            // Reset form, hide error message, and display success message
            form.reset();
            errorMessage.style.display = 'none';
            successMessage.style.display = 'block';
            // Hide success message after 3 seconds
            setTimeout(() => {
              successMessage.style.display = 'none';
            }, 3000);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          errorMessage.textContent = 'An error occurred while submitting the form.';
          errorMessage.style.display = 'block';
        });
      });
    });
  </script>
</body>
</html>
