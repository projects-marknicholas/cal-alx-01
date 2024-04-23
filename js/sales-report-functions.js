document.addEventListener('DOMContentLoaded', function () {
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  const tableBody = document.querySelector('#pdfTable tbody');
  const printButton = document.querySelector('.print-button');

  // Function to generate PDF for sales report with custom design and CSS styles
  function generatePDF(startDate, endDate, data, totalSales, mostOrderedItem, leastOrderedItem) {
    const opt = {
      margin: 1,
      filename: 'sales_report.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    // Construct HTML content with CSS styles
    const htmlContent = `
      <style>
        /* CSS styles for the sales report */
        .custom-sales-report {
          font-family: Arial, sans-serif;
          max-width: 800px;
          margin: 0 auto;
          padding: 20px;
          background-color: #f5f5f5;
          border-radius: 10px;
        }

        .custom-sales-report h1 {
          text-align: center;
          color: #333;
        }

        .custom-sales-report p {
          margin-bottom: 10px;
          color: #666;
        }

        .custom-sales-report table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
        }

        .custom-sales-report th, .custom-sales-report td {
          border: 1px solid #ddd;
          padding: 8px;
          text-align: left;
        }

        .custom-sales-report th {
          background-color: #f2f2f2;
        }
      </style>
      <div class="custom-sales-report">
        <h1>Sales Report</h1>
        <p>Start Date: ${startDate}</p>
        <p>End Date: ${endDate}</p>
        <p>Total Sales: ₱ ${totalSales}</p>
        <p>Most Ordered Item: ${mostOrderedItem}</p>
        <p>Least Ordered Item: ${leastOrderedItem}</p>
        <!-- Sales table -->
        <table>
          <thead>
            <tr>
              <th>Product Name</th>
              <th>Quantity</th>
              <th>Total Unit Price</th>
            </tr>
          </thead>
          <tbody>
            ${data.map(item => `
              <tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>₱ ${item.total_unit_price}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;

    // Convert the custom HTML content to PDF
    html2pdf().from(htmlContent).set(opt).save();
  }

  // Function to fetch data from the endpoint and populate the table
  function fetchData(startDate, endDate) {
    fetch(`http://localhost/web-app/cap-alx-01/backend/api/sales-report?start_date=${startDate}&end_date=${endDate}`)
      .then(response => response.json())
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
          generatePDF(startDate, endDate, data, totalSales, mostOrderedItem, leastOrderedItem);
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
