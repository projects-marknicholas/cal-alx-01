document.addEventListener('DOMContentLoaded', function () {
  const tableBody = document.querySelector('#stocksTable tbody');
  const successMessage = document.getElementById('success-message');
  const errorMessage = document.getElementById('error-message');
  const searchInput = document.getElementById('search');
  let data = []; // Store the original data for filtering

  // Function to fetch data from the endpoint and populate the table
  function fetchData() {
    fetch('http://localhost/web-app/cap-alx-01/backend/api/stocks')
      .then(response => response.json())
      .then(responseData => {
        data = responseData; // Store the original data for filtering
        renderTable(data);
      })
      .catch(error => console.error('Error fetching data:', error));
  }

  // Function to render the table with the given data
  function renderTable(data) {
    // Clear existing table rows
    tableBody.innerHTML = '';

    // Loop through the data and create table rows
    data.forEach(item => {
      const row = createTableRow(item);
      tableBody.appendChild(row);
    });
  }

  // Function to create a table row
  function createTableRow(item) {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><input type="text" value="${item.product_name}"></td>
      <td><input type="text" value="${item.description}"></td>
      <td><input type="number" value="${item.quantity}"></td>
      <td><input type="number" value="${item.unit_price}"></td>
      <td><button class="update" data-product-id="${item.product_id}">Update</button>&nbsp;<button class="delete" data-product-id="${item.product_id}">Delete</button></td>
    `;
    return row;
  }

  // Function to handle delete button click
  function handleDeleteButtonClick(button) {
    const row = button.parentElement.parentElement;
    const productId = button.getAttribute('data-product-id');
    row.remove();

    // Send DELETE request to delete endpoint
    fetch(`http://localhost/web-app/cap-alx-01/backend/api/delete-stock?product_id=${productId}`, {
      method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        successMessage.textContent = 'Stock deleted successfully!';
        successMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        setTimeout(() => {
          successMessage.style.display = 'none';
        }, 3000);
      } else {
        errorMessage.textContent = data.error;
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
      }
    })
    .catch(error => console.error('Error deleting product:', error));
  }

  // Function to handle update button click
  function handleUpdateButtonClick(button) {
    const productId = button.getAttribute('data-product-id');
    const productName = button.parentElement.parentElement.querySelector('td:nth-child(1) input').value;
    const description = button.parentElement.parentElement.querySelector('td:nth-child(2) input').value;
    const quantity = button.parentElement.parentElement.querySelector('td:nth-child(3) input').value;
    const unitPrice = button.parentElement.parentElement.querySelector('td:nth-child(4) input').value;

    // Create FormData object to send updated product data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('description', description);
    formData.append('quantity', quantity);
    formData.append('unit_price', unitPrice);

    // Send POST request to update endpoint
    fetch(`http://localhost/web-app/cap-alx-01/backend/api/update-stock?product_id=${productId}`, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        successMessage.textContent = 'Stock updated successfully!';
        successMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        setTimeout(() => {
          successMessage.style.display = 'none';
        }, 3000);
      } else {
        errorMessage.textContent = data.error;
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
      }
    })
    .catch(error => console.error('Error updating product:', error));
  }

  // Function to filter the table based on search input
  function filterTable(searchText) {
    const filteredData = data.filter(item => {
      return Object.values(item).some(val => typeof val === 'string' && val.toLowerCase().includes(searchText));
    });
    renderTable(filteredData);
  }

  // Add event listener to search input
  searchInput.addEventListener('input', function() {
    const searchText = searchInput.value.trim().toLowerCase();
    filterTable(searchText);
  });

  // Add event listener to table for delegated event handling
  tableBody.addEventListener('click', function(event) {
    const target = event.target;
    if (target.classList.contains('delete')) {
      handleDeleteButtonClick(target);
    } else if (target.classList.contains('update')) {
      handleUpdateButtonClick(target);
    }
  });

  // Initial data fetch
  fetchData();
});