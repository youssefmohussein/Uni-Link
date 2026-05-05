/**
 * Terra Fusion Admin Script
 * Handles Sidebar toggle and Modal population for Edit actions.
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log("Terra Fusion Admin Script Loaded");

    // Sidebar Toggle Logic
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarMenu');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }
});

/**
 * Menu Item Edit
 */
function editItem(item) {
    const modalLabel = document.getElementById('menuModalLabel');
    if (modalLabel) modalLabel.innerText = 'Edit Meal';

    setValue('itemId', item.meal_id);
    setValue('name', item.meal_name);
    setValue('meal_type', item.meal_type);
    setValue('price', item.price);
    setValue('quantity', item.quantity);
    setValue('description', item.description);

    // Store current image in hidden field (for when no new file is uploaded)
    setValue('current_image', item.image);

    // Show current image name
    const imagePreview = document.getElementById('current_image_preview');
    const imageName = document.getElementById('current_image_name');
    if (imagePreview && imageName && item.image) {
        imageName.textContent = item.image;
        imagePreview.style.display = 'block';
    }

    const isAvailable = document.getElementById('is_available');
    if (isAvailable) {
        isAvailable.checked = (item.availability === 'Available');
    }
}

/**
 * User Edit
 */
function editUser(user) {
    const modalLabel = document.getElementById('userModalLabel');
    if (modalLabel) modalLabel.innerText = 'Edit Staff Member';

    const form = document.getElementById('userForm');
    if (form) form.action = 'index.php?page=users&action=update';

    setValue('userId', user.user_id);
    setValue('email', user.email);
    setValue('role', user.role);

    const passwordField = document.getElementById('password');
    if (passwordField) passwordField.required = false;
}

/**
 * Reservation Edit
 * Logic:
 * 1. Listen to Edit click (via onclick='editReservation(this)')
 * 2. Read data-* attributes (dataset)
 * 3. Populate hidden ID input and visible fields
 * 4. FIELD NAMES MATCH: reservationId, customer_name, contact_phone, reservation_date, reservation_time, party_size
 */
function editReservation(btn) {
    const modalLabel = document.getElementById('reservationModalLabel');
    if (modalLabel) modalLabel.innerText = 'Edit Reservation';

    const form = document.getElementById('reservationForm');
    // Ensure correct action - controller handles both save/update via 'save' action
    if (form) form.action = 'index.php?page=reservations&action=save';

    const d = btn.dataset;

    // Populate hidden input 'reservation_id'
    setValue('reservationId', d.id);

    // Populate visible inputs
    setValue('customer_name', d.name);
    setValue('contact_phone', d.phone);
    setValue('reservation_date', d.date);
    setValue('reservation_time', d.time);
    setValue('party_size', d.size);
}

/**
 * Reset Forms
 */
function resetForm() {
    const modalLabel = document.getElementById('menuModalLabel');
    if (modalLabel) modalLabel.innerText = 'Add Meal';
    setValue('itemId', '');
    setValue('name', '');
    setValue('meal_type', 'Lunch');
    setValue('price', '');
    setValue('quantity', '0');
    setValue('description', '');
    setValue('current_image', '');

    // Clear file input
    const fileInput = document.getElementById('meal_image');
    if (fileInput) fileInput.value = '';

    // Hide image preview
    const imagePreview = document.getElementById('current_image_preview');
    if (imagePreview) imagePreview.style.display = 'none';

    const isAvailable = document.getElementById('is_available');
    if (isAvailable) isAvailable.checked = true;
}

function resetUserForm() {
    const modalLabel = document.getElementById('userModalLabel');
    if (modalLabel) modalLabel.innerText = 'Add New Staff';
    const form = document.getElementById('userForm');
    if (form) form.action = 'index.php?page=users&action=create';
    setValue('userId', '');
    setValue('email', '');
    setValue('role', 'Waiter');
    setValue('password', '');
    const passwordField = document.getElementById('password');
    if (passwordField) passwordField.required = true;
}

function resetReservationForm() {
    const modalLabel = document.getElementById('reservationModalLabel');
    if (modalLabel) modalLabel.innerText = 'New Reservation';

    const form = document.getElementById('reservationForm');
    if (form) form.action = 'index.php?page=reservations&action=save';

    // Clear ID for new creation
    setValue('reservationId', '');

    setValue('customer_name', '');
    setValue('contact_phone', '');
    setValue('reservation_date', '');
    setValue('reservation_time', '');
    setValue('party_size', '');
}

/**
 * Helper to set value safely
 */
function setValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.value = value;
}
