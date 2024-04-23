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
      <button class="print-button">Print PDF Report</button>
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

  <script src="../js/sidebar-effects.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const startDateInput = document.getElementById('start_date');
      const endDateInput = document.getElementById('end_date');
      const tableBody = document.querySelector('#pdfTable tbody');

      // Function to fetch data from the endpoint and populate the table
      function fetchData(startDate, endDate) {
        fetch(`http://localhost/web-app/cap-alx-01/backend/api/sales-report?start_date=${startDate}&end_date=${endDate}`)
          .then(response => response.json())
          .then(data => {
            // Clear existing table rows
            tableBody.innerHTML = '';

            // Loop through the data and create table rows
            data.forEach(item => {
              const row = document.createElement('tr');
              row.innerHTML = `
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>₱ ${item.total_unit_price}</td>
              `;
              tableBody.appendChild(row);
            });
          })
          .catch(error => console.error('Error fetching data:', error));
      }

      // Event listener for search inputs change
      startDateInput.addEventListener('change', function () {
        const startDate = this.value;
        const endDate = endDateInput.value;
        fetchData(startDate, endDate);
      });

      endDateInput.addEventListener('change', function () {
        const startDate = startDateInput.value;
        const endDate = this.value;
        fetchData(startDate, endDate);
      });
    });
  </script>
</body>
</html>