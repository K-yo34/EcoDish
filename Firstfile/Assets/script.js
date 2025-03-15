/**
 * EcoDish main JavaScript file
 * Handles client-side functionality like form validation and cart interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', validateForm);
    });
    
    // Quantity buttons in cart
    const quantityBtns = document.querySelectorAll('.quantity-btn');
    quantityBtns.forEach(btn => {
        btn.addEventListener('click', handleQuantityChange);
    });
    
    // Add to cart buttons
    const addToCartBtns = document.querySelectorAll('.add-to-cart');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', handleAddToCart);
    });
});

/**
 * Validate form inputs
 * @param {Event} e - The form submit event
 */
function validateForm(e) {
    const form = e.target;
    let isValid = true;
    
    // Reset previous error messages
    const errorElements = form.querySelectorAll('.field-error');
    errorElements.forEach(el => el.remove());
    
    // Check required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            showFieldError(field, 'This field is required');
        }
    });
    
    // Validate email fields
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value.trim() && !isValidEmail(field.value)) {
            isValid = false;
            showFieldError(field, 'Please enter a valid email address');
        }
    });
    
    // Validate password fields
    const passwordField = form.querySelector('input[name="password"]');
    const confirmPasswordField = form.querySelector('input[name="confirm_password"]');
    
    if (passwordField && confirmPasswordField) {
        if (passwordField.value !== confirmPasswordField.value) {
            isValid = false;
            showFieldError(confirmPasswordField, 'Passwords do not match');
        }
    }
    
    if (!isValid) {
        e.preventDefault();
    }
}

/**
 * Display error message for a form field
 * @param {HTMLElement} field - The form field
 * @param {string} message - The error message
 */
function showFieldError(field, message) {
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    errorElement.style.color = '#e53e3e';
    errorElement.style.fontSize = '0.875rem';
    errorElement.style.marginTop = '5px';
    
    field.parentNode.appendChild(errorElement);
    field.style.borderColor = '#e53e3e';
}

/**
 * Validate email format
 * @param {string} email - The email to validate
 * @return {boolean} True if valid, false otherwise
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Handle quantity change in cart
 * @param {Event} e - The click event
 */
function handleQuantityChange(e) {
    const btn = e.target;
    const action = btn.dataset.action;
    const itemId = btn.dataset.id;
    const itemType = btn.dataset.type;
    const quantityInput = document.querySelector(`input[name="quantity[${itemType}][${itemId}]"]`);
    
    let quantity = parseInt(quantityInput.value);
    
    if (action === 'increase') {
        quantity++;
    } else if (action === 'decrease' && quantity > 1) {
        quantity--;
    }
    
    quantityInput.value = quantity;
    
    // Update cart via AJAX
    updateCartItem(itemId, itemType, quantity);
}

/**
 * Update cart item quantity via AJAX
 * @param {string} itemId - The item ID
 * @param {string} itemType - The item type (menu or fresh)
 * @param {number} quantity - The new quantity
 */
function updateCartItem(itemId, itemType, quantity) {
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('item_type', itemType);
    formData.append('quantity', quantity);
    formData.append('action', 'update');
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update subtotal and total
            document.querySelector(`#subtotal-${itemType}-${itemId}`).textContent = data.subtotal;
            document.querySelector('#cart-total').textContent = data.total;
            
            // Update cart count in header
            document.querySelector('.cart-count').textContent = data.count;
        }
    })
    .catch(error => {
        console.error('Error updating cart:', error);
    });
}

/**
 * Handle add to cart button click
 * @param {Event} e - The click event
 */
function handleAddToCart(e) {
    e.preventDefault();
    const btn = e.target;
    const itemId = btn.dataset.id;
    const itemType = btn.dataset.type;
    
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('item_type', itemType);
    formData.append('quantity', 1);
    formData.append('action', 'add');
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const message = document.createElement('div');
            message.className = 'add-to-cart-message';
            message.textContent = 'Added to cart!';
            message.style.position = 'fixed';
            message.style.top = '20px';
            message.style.right = '20px';
            message.style.backgroundColor = '#4a8f3d';
            message.style.color = 'white';
            message.style.padding = '10px 20px';
            message.style.borderRadius = '4px';
            message.style.zIndex = '1000';
            
            document.body.appendChild(message);
            
            // Update cart count in header
            document.querySelector('.cart-count').textContent = data.count;
            
            // Remove message after 3 seconds
            setTimeout(() => {
                message.remove();
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
    });
}