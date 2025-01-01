document.addEventListener('DOMContentLoaded', function () {
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  const tableBody = document.querySelector('#pdfTable tbody');
  const printButton = document.querySelector('.print-button');

  // Function to fetch data from the endpoint and populate the table
  function fetchData(startDate, endDate) {
    fetch(`http://localhost/web-app/lspu-cmi/backend/api/sales-report?start_date=${startDate}&end_date=${endDate}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        // Calculate total sales
        const totalSales = data.reduce((acc, curr) => acc + parseFloat(curr.total_unit_price), 0).toFixed(2);

        // Calculate most ordered item
        const mostOrderedItem = data.reduce((prev, curr) => (prev.quantity > curr.quantity) ? prev : curr).product_name;

        // Calculate least ordered item
        const leastOrderedItem = data.reduce((prev, curr) => (prev.quantity < curr.quantity) ? prev : curr).product_name;

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

        // Attach event listener to "Print PDF Report" button
        printButton.addEventListener('click', function () {
          // Generate PDF after populating the table
          printSalesReport(startDate, endDate);
        });
      })
      .catch(error => console.error('Error fetching data:', error));
  }

  // Function to fetch data from the endpoint and print the sales report
  function printSalesReport(startDate, endDate) {
    fetch(`http://localhost/web-app/lspu-cmi/backend/api/print-sales-report?start_date=${startDate}&end_date=${endDate}`)
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
