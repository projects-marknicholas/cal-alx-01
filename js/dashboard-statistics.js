function fetchDataAndRenderCharts() {
  fetch('http://localhost/web-app/cap-alx-01/backend/api/dashboard')
    .then(response => response.json())
    .then(data => {
      // Update sales data
      const ctx = document.getElementById('myChart');
      const ctx2 = document.getElementById('myChart2');
      const salesData = data.Sales.map(month => month.total_sales);
      const salesLabels = data.Sales.map(month => month.month); // Extracting month labels
      const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: salesLabels, // Use extracted month labels here
          datasets: [{
            label: 'Sales',
            data: salesData, // Use salesData here
            borderWidth: 1,
            borderRadius: 30,
            barThickness: 12,
            backgroundColor: 'rgba(114, 92, 255, 1)',
            borderColor: 'rgba(114, 92, 255, 1)',
            hoverBackgroundColor: 'rgba(28, 30, 35, 1)',
            hoverBorderColor: 'rgba(28, 30, 35, 1)',
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return '₱ ' + value;
                },
                stepSize: 50,
              },
            },
            x: {
              grid: {
                display: false
              }
            }
          },
          plugins: {
            legend: {
              display: false,
              labels: {
                font: {
                  size: 12,
                  family: "'Plus Jakarta Sans', sans-serif",
                  lineHeight: 18,
                  weight: 600
                }
              }
            }
          }
        }
      });

      // Update stocks data
      const stocksData = data.Stocks.map(stock => stock.total_quantity);
      const stocksChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
          datasets: [{
            label: 'Stocks',
            data: stocksData,
            borderRadius: 5,
            cutout: 80,
            backgroundColor: [
              'rgb(235, 124, 166)',
              'rgb(255, 172, 200)',
              'rgb(204, 111, 248)',
              'rgb(124, 92, 252)',
              'rgb(92, 175, 252)',
              'rgb(161, 169, 254)'
            ],
            hoverOffset: 4,
            spacing: 8
          }]
        },
        options: {
          plugins: {
            legend: {
              display: false
            }
          }
        }
      });
    })
    .catch(error => console.error('Error fetching data:', error));
}

fetchDataAndRenderCharts();
setInterval(fetchDataAndRenderCharts, 1000);
