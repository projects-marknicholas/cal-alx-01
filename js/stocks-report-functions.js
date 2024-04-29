document.addEventListener('DOMContentLoaded', function () {
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  const tableBody = document.querySelector('#pdfTable tbody');
  const printButton = document.querySelector('.print-button');

  // Function to fetch data from the endpoint and populate the table
  function fetchData(startDate, endDate) {
    fetch(`http://localhost/web-app/cap-alx-01/backend/api/stocks-report?start_date=${startDate}&end_date=${endDate}`)
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
            <td>${item.unit_price}</td>
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

  // Event listener for "Print PDF Report" button
  printButton.addEventListener('click', function () {
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    printStocksReport(startDate, endDate);
  });

  // Function to print stocks report
  function printStocksReport(startDate, endDate) {
    fetch(`http://localhost/web-app/cap-alx-01/backend/api/print-stocks-report?start_date=${startDate}&end_date=${endDate}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.text();
      })
      .then(data => {
        // Trigger browser's print functionality
        window.print();
      })
      .catch(error => console.error('Error fetching data:', error));
  }
});
