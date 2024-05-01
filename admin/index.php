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
    <div class="container">
      <main class="main-content">
            <div class="bottom-container">
                <div class="bottom-container__left">
                    <div class="box spending-box">
                        <div class="header-container">
                            <h3 class="section-header">Sales this Year</h3>
                        </div>
                        <div class="bar-chart">
                            <canvas id="myChart" height="220px" width="660px"></canvas>
                        </div>
                    </div>
                    <div class="box total-box">
                        <div class="total-box__left">
                            <div class="header-container">
                                <h3 class="section-header">Total Sales Last Month</h3>
                                <svg class="up-arrow" width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="42" height="42" rx="8" fill="#F6F7F9"/>
                                    <path d="M27.0702 18.57L21.0002 12.5L14.9302 18.57" stroke="#7FB519" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 29.5V12.67" stroke="#7FB519" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>                                
                            </div>
                            <h1 class="price" id="dash-sales-last-month">₱ 0.00<span class="price-currency">(Philippine Peso)</span></h1>
                            <p><span class="percentage-increase">20%</span> increase compared to last week</p>                         
                        </div>
                        <div class="total-box__right">
                            <div class="header-container">
                                <h3 class="section-header">Total Sales this Month</h3>
                                <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="42" height="42" rx="8" fill="#F6F7F9"/>
                                    <path d="M27.0702 23.43L21.0002 29.5L14.9302 23.43" stroke="#FF4423" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21 12.5V29.33" stroke="#FF4423" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>                                                                    
                            </div>
                            <h1 class="price" id="dash-sales-this-month">₱ 0.00<span class="price-currency">(Philippine Peso)</span></h1>                            
                            <p><span class="percentage-decrease">10%</span> decrease compared to last week</p>
                        </div>
                    </div>
                    <div class="box transaction-box">
                        <div class="header-container">
                            <h3 class="section-header">Recent Orders</h3>                               
                        </div>
                        <table class="transaction-history">
                            <tr>
                                <th>Qty</th>
                                <th>Product</th>
                                <th>Date
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.96004 4.47498L6.70004 7.73498C6.31504 8.11998 5.68504 8.11998 5.30004 7.73498L2.04004 4.47498" stroke="#90A3BF" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>                                        
                                </th>
                                <th>Amount
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.96004 4.47498L6.70004 7.73498C6.31504 8.11998 5.68504 8.11998 5.30004 7.73498L2.04004 4.47498" stroke="#90A3BF" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>                                        
                                </th>
                                <th>Status
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.96004 4.47498L6.70004 7.73498C6.31504 8.11998 5.68504 8.11998 5.30004 7.73498L2.04004 4.47498" stroke="#90A3BF" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>                                       
                                </th>
                            </tr>
                        </table>
                    </div>                   
                </div>
                <div class="bottom-container__right">
                    <div class="box">
                        <div class="header-container">
                            <h3 class="section-header">Sales Today</h3>
                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10.4166C3.9 10.4166 3 11.3541 3 12.5C3 13.6458 3.9 14.5833 5 14.5833C6.1 14.5833 7 13.6458 7 12.5C7 11.3541 6.1 10.4166 5 10.4166Z" stroke="#1A202C" stroke-width="1.5"/>
                                <path d="M19 10.4166C17.9 10.4166 17 11.3541 17 12.5C17 13.6458 17.9 14.5833 19 14.5833C20.1 14.5833 21 13.6458 21 12.5C21 11.3541 20.1 10.4166 19 10.4166Z" stroke="#1A202C" stroke-width="1.5"/>
                                <path d="M12 10.4166C10.9 10.4166 10 11.3541 10 12.5C10 13.6458 10.9 14.5833 12 14.5833C13.1 14.5833 14 13.6458 14 12.5C14 11.3541 13.1 10.4166 12 10.4166Z" stroke="#1A202C" stroke-width="1.5"/>
                            </svg>                                                           
                        </div>
                        <h1 class="price" id="dash-sales-today">₱ 0.00<span class="price-currency">(Philippine Peso)</span></h1>
                    </div>
                    <div class="box spending-box">
                        <div class="header-container">
                            <h3 class="section-header">Remaining Stocks</h3>
                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 10.4166C3.9 10.4166 3 11.3541 3 12.5C3 13.6458 3.9 14.5833 5 14.5833C6.1 14.5833 7 13.6458 7 12.5C7 11.3541 6.1 10.4166 5 10.4166Z" stroke="#1A202C" stroke-width="1.5"/>
                                <path d="M19 10.4166C17.9 10.4166 17 11.3541 17 12.5C17 13.6458 17.9 14.5833 19 14.5833C20.1 14.5833 21 13.6458 21 12.5C21 11.3541 20.1 10.4166 19 10.4166Z" stroke="#1A202C" stroke-width="1.5"/>
                                <path d="M12 10.4166C10.9 10.4166 10 11.3541 10 12.5C10 13.6458 10.9 14.5833 12 14.5833C13.1 14.5833 14 13.6458 14 12.5C14 11.3541 13.1 10.4166 12 10.4166Z" stroke="#1A202C" stroke-width="1.5"/>
                            </svg>                                                           
                        </div>
                        <div class="pie-chart">
                            <canvas id="myChart2" height="220px" width="220px"></canvas>
                        </div>
                        <div class="overall-spending">
                            <h4>Overall Stocks</h4>
                            <span id="dash-remaining-stocks">0</span>
                        </div>
                        <div class="pie-chart__labels">
                            
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
  </section>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1/chart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../js/sidebar-effects.js"></script>
  <script src="../js/dashboard-statistics.js"></script>
  <script>
    // Function to fetch dashboard data from the API endpoint
    async function fetchDashboardData() {
      try {
        const response = await fetch('http://localhost/web-app/cap-alx-01/backend/api/dashboard');
        if (!response.ok) {
          throw new Error('Failed to fetch data');
        }
        return await response.json();
      } catch (error) {
        console.error('Error fetching data:', error);
        return null;
      }
    }

    // Initial call to fetch dashboard data
    fetchDashboardData()
      .then(data => {
        document.getElementById('dash-sales-last-month').textContent = "₱ " + data.Monthly.sales_last_month.toFixed(2) + '';
        document.getElementById('dash-sales-this-month').textContent = "₱ " + data.Monthly.sales_this_month.toFixed(2);
        document.getElementById('dash-sales-today').textContent = "₱ " + data.Daily.sales_today.toFixed(2);
        document.getElementById('dash-remaining-stocks').textContent = data.TotalStockQuantity;

        // Display orders
        const ordersTable = document.querySelector('.transaction-history');
        data.Orders.forEach(order => {
          const row = `
            <tr>
              <td>${order.quantity}</td>
              <td>${order.product_name}</td>
              <td>${new Date(order.sell_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
              <td>₱ ${order.total_amount}</td>
              <td>
                <svg class="status" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect width="16" height="16" rx="8" fill="#BCE455" fill-opacity="0.3"/>
                  <circle cx="8" cy="8" r="4" fill="#7FB519"/>
                </svg>                                        
                Completed
              </td>
            </tr>
          `;
          ordersTable.innerHTML += row;
        });
        
        const stocksItems = document.querySelector('.pie-chart__labels');
        data.Stocks.forEach((stock, index) => {
          const colorClasses = ['first', 'second', 'third', 'fourth', 'fifth', 'sixth'];
          
          const colorClass = colorClasses[index % colorClasses.length];

          const row = `
            <div class="pie-chart__labels-item">
              <div class="label">
                <div class="label__color ${colorClass}"></div>
                ${stock.product_name}
              </div>
              ${stock.total_quantity}
            </div>
          `;

          // Append the row to the stocksItems container
          stocksItems.innerHTML += row;
        });
      })
      .catch(error => {
        console.error('Error fetching dashboard data:', error);
      });
    
    fetchDashboardData();
    setInterval(() => {
      fetchDashboardData();
    }, 1000);
  </script>
</body>
</html>