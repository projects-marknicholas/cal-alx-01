document.addEventListener('DOMContentLoaded', function () {
  const loginForm = document.getElementById('login-form');
  const signupForm = document.getElementById('signup-form');
  const errorMessage = document.getElementById('error-message');

  loginForm.addEventListener('submit', function (event) {
    event.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    // Send login request to backend
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    fetch('http://localhost/web-app/cap-alx-01/backend/api/login', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        errorMessage.style.display = 'block';
        errorMessage.textContent = data.error;
      } else {
        // Handle successful login
        errorMessage.style.display = 'none';
        window.location.href = 'admin';
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });

  signupForm.addEventListener('submit', function (event) {
    event.preventDefault();
    const firstName = document.getElementById('signup-first-name').value;
    const lastName = document.getElementById('signup-last-name').value;
    const email = document.getElementById('signup-email').value;
    const password = document.getElementById('signup-password').value;
    const confirmPassword = document.getElementById('signup-confirm-password').value;

    // Send register request to backend
    const formData = new FormData();
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('confirm_password', confirmPassword);

    fetch('http://localhost/web-app/cap-alx-01/backend/api/register', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        errorMessage.style.display = 'block';
        errorMessage.textContent = data.error;
      } else {
        // Handle successful registration
        errorMessage.style.display = 'none';
        window.location.href = './';
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });
});
  