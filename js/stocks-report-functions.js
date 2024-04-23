document.addEventListener('DOMContentLoaded', function () {
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  const tableBody = document.querySelector('#pdfTable tbody');
  const printButton = document.querySelector('.print-button');

  // Function to generate PDF for stocks report with custom design and CSS styles
  function generatePDF(startDate, endDate) {
    const opt = {
      margin: 1,
      filename: 'stocks_report.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    // Fetch data from the endpoint
    fetch(`http://localhost/web-app/cap-alx-01/backend/api/stocks-report?start_date=${startDate}&end_date=${endDate}`)
      .then(response => response.json())
      .then(data => {
        // Construct HTML content with CSS styles
        const htmlContent = `
          <style>
            /* CSS styles for the stocks report */
            .custom-stocks-report {
              font-family: Arial, sans-serif;
              max-width: 800px;
              margin: 0 auto;
              padding: 20px;
              background-color: #f5f5f5;
              border-radius: 10px;
            }

            .custom-stocks-report h1 {
              text-align: center;
              color: #333;
            }

            .custom-stocks-report p {
              margin-bottom: 10px;
              color: #666;
            }

            .custom-stocks-report table {
              width: 100%;
              border-collapse: collapse;
              margin-top: 20px;
            }

            .custom-stocks-report th, .custom-stocks-report td {
              border: 1px solid #ddd;
              padding: 8px;
              text-align: left;
            }

            .custom-stocks-report th {
              background-color: #f2f2f2;
            }
          </style>
          <div class="custom-stocks-report">
            <h1>Stocks Report</h1>
            <p>Start Date: ${startDate}</p>
            <p>End Date: ${endDate}</p>
            <!-- Stocks table -->
            <table>
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Quantity</th>
                  <th>Unit Price</th>
                </tr>
              </thead>
              <tbody>
                ${data.map(item => `
                  <tr>
                    <td>${item.product_name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.unit_price}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        `;

        // Convert the custom HTML content to PDF
        html2pdf().from(htmlContent).set(opt).save();
      })
      .catch(error => console.error('Error fetching data:', error));
  } 

  // Event listener for "Print PDF Report" button
  printButton.addEventListener('click', function () {
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    generatePDF(startDate, endDate);
  });

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
});