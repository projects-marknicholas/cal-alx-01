document.addEventListener('DOMContentLoaded', function () {
  const salesGrid = document.getElementById('salesGrid');
  const selectedItemsDiv = document.getElementById('selectedItems');
  const receiptDiv = document.getElementById('receipt');
  const selectedItems = [];

  // Function to fetch data from the endpoint and populate the sales grid
  function fetchStocks() {
    fetch('http://localhost/web-app/cap-alx-01/backend/api/stocks')
      .then(response => response.json())
      .then(data => {
        // Clear existing items
        salesGrid.innerHTML = '';

        // Loop through the data and create sales items
        data.forEach(stock => {
          const item = createSalesItem(stock);
          salesGrid.appendChild(item);

          // Add click event listener to toggle active state
          item.addEventListener('click', function() {
            toggleActive(this, stock);
            displaySelectedItems();
          });
        });
      })
      .catch(error => console.error('Error fetching stocks:', error));
  }

  // Function to create a sales item
  function createSalesItem(stock) {
    const item = document.createElement('div');
    item.classList.add('item');
    item.innerHTML = `
      <div class="item-image">
        <img src="../img/no-product.png" alt="${stock.product_name}">
      </div>
      <div class="item-info">
        <h1>${stock.product_name}</h1>
        <p>${stock.description}</p>
        <p>Unit Price: ${stock.unit_price}</p>
      </div>
    `;
    return item;
  }

  // Function to toggle active state
  function toggleActive(element, stock) {
    const isActive = element.classList.contains('active');
    if (isActive) {
      element.classList.remove('active');
      removeItemFromSelected(stock.product_id);
    } else {
      // Remove active state from all items
      const allItems = salesGrid.querySelectorAll('.item');
      allItems.forEach(item => item.classList.remove('active'));

      // Add active state to clicked item
      element.classList.add('active');
      addItemToSelected(stock);
    }
  }

  // Function to add item to selected array
  function addItemToSelected(stock) {
    const item = {
      product_id: stock.product_id,
      product_name: stock.product_name,
      description: stock.description,
      unit_price: stock.unit_price,
      quantity: 1 // Default quantity is 1
    };
    selectedItems.push(item);
    calculateTotals(); // Update totals when a new item is added
  }

  // Function to remove item from selected array
  function removeItemFromSelected(productId) {
    const index = selectedItems.findIndex(item => item.product_id === productId);
    if (index !== -1) {
      selectedItems.splice(index, 1);
      calculateTotals(); // Update totals when an item is removed
    }
  }

  // Function to display selected items
  function displaySelectedItems() {
    selectedItemsDiv.innerHTML = '';
    selectedItems.forEach(item => {
      const selectedItemDiv = document.createElement('div');
      selectedItemDiv.classList.add('selected-item');
      selectedItemDiv.innerHTML = `
        <p>Product ID: ${item.product_id}</p>
        <p>Name: ${item.product_name}</p>
        <p>Description: ${item.description}</p>
        <p>Unit Price: ${item.unit_price}</p>
        <input type="number" class="quantity-input" value="${item.quantity}" min="1">
        <button class="delete-button">Delete</button>
      `;
      // Add event listener to update quantity when input changes
      const quantityInput = selectedItemDiv.querySelector('.quantity-input');
      quantityInput.addEventListener('input', function() {
        updateQuantity(item.product_id, parseInt(this.value));
      });
      // Add event listener to delete button
      const deleteButton = selectedItemDiv.querySelector('.delete-button');
      deleteButton.addEventListener('click', function() {
        removeItemFromSelected(item.product_id);
        displaySelectedItems();
      });
      // Prepend the selected item div to the selected items container
      selectedItemsDiv.prepend(selectedItemDiv);
    });
    calculateTotals(); // Update totals when selected items change
  }

  // Function to update quantity for selected item
  function updateQuantity(productId, newQuantity) {
    const selectedItem = selectedItems.find(item => item.product_id === productId);
    if (selectedItem) {
      selectedItem.quantity = newQuantity;
      calculateTotals(); // Update totals when quantity changes
    }
  }

  // Function to calculate subtotal, tax, and total
  function calculateTotals() {
    let subtotal = 0;
    let tax = 0;

    // Calculate subtotal
    selectedItems.forEach(item => {
      subtotal += item.unit_price * item.quantity;
    });

    // Calculate tax (if any)
    // For example, let's say the tax rate is 10%
    tax = 0.1 * subtotal;

    // Calculate total
    const total = subtotal + tax;

    // Update the DOM elements with the calculated values
    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('tax').textContent = tax.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
  }

  // Function to reset cash and change fields to zero
  function resetCashAndChange() {
    document.getElementById('cashInput').value = '';
    document.getElementById('change').textContent = '0.00';
  }

  function placeOrder() {
    const requestData = selectedItems.map(item => ({
      product_id: item.product_id,
      quantity: item.quantity
    }));

    fetch('http://localhost/web-app/cap-alx-01/backend/api/add-sell', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestData)
    })
    .then(response => {
      if (response.ok) {
        // Clear selected items upon successful order placement
        selectedItems.splice(0, selectedItems.length);
        displaySelectedItems(); // Update the display
        // Reset cash and change fields
        resetCashAndChange();
        // Show success message
        document.getElementById('success-message').textContent = 'Order successfully placed';
        document.getElementById('success-message').style.display = 'block';
        setTimeout(function() {
          document.getElementById('success-message').style.display = 'none';
        }, 3000); // Hide success message after 3 seconds
      } else {
        throw new Error('Failed to place order');
      }
    })
    .catch(error => {
      console.error('Error placing order:', error);
      // Show error message
      document.getElementById('error-message').textContent = 'Failed to place order';
      document.getElementById('error-message').style.display = 'block';
      setTimeout(function() {
        document.getElementById('error-message').style.display = 'none';
      }, 3000); // Hide error message after 3 seconds
    });
  }

  // Event listener for the "Place Order" button
  document.getElementById('placeOrderBtn').addEventListener('click', function() {
    const cashInput = document.getElementById('cashInput');
    const cashAmount = parseFloat(cashInput.value);
    const subtotal = parseFloat(document.getElementById('subtotal').textContent);

    if (isNaN(cashAmount) || cashAmount < subtotal) {
      alert('Please enter a valid cash amount.');
      return;
    }

    // Place the order
    placeOrder();
  });

  // Function to calculate change
  function calculateChange(cashAmount, total) {
    return cashAmount - total;
  }

  // Event listener for cash input change
  document.getElementById('cashInput').addEventListener('input', function() {
    const cashAmount = parseFloat(this.value);
    const total = parseFloat(document.getElementById('total').textContent);

    if (!isNaN(cashAmount)) {
      const change = calculateChange(cashAmount, total);
      document.getElementById('change').textContent = change.toFixed(2);
    } else {
      // Handle invalid input
      document.getElementById('change').textContent = '0.00';
    }
  });

  // Initial data fetch
  fetchStocks();
});