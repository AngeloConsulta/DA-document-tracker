// Import SweetAlert2
import Swal from 'sweetalert2';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Toast notification configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    },
    customClass: {
        popup: 'colored-toast',
        title: 'text-base font-medium',
        htmlContainer: 'text-sm'
    },
    style: {
        background: '#fff',
        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        borderRadius: '0.5rem',
        padding: '1rem'
    }
});

// Success notification
window.showSuccess = function(message) {
    Toast.fire({
        icon: 'success',
        title: 'Success!',
        text: message,
        iconColor: '#10B981',
        background: '#F0FDF4',
        color: '#065F46'
    });
};

// Error notification
window.showError = function(message) {
    Toast.fire({
        icon: 'error',
        title: 'Error!',
        text: message,
        iconColor: '#EF4444',
        background: '#FEF2F2',
        color: '#991B1B'
    });
};

// Info notification
window.showInfo = function(message) {
    Toast.fire({
        icon: 'info',
        title: 'Info',
        text: message,
        iconColor: '#3B82F6',
        background: '#EFF6FF',
        color: '#1E40AF'
    });
};

// Warning notification
window.showWarning = function(message) {
    Toast.fire({
        icon: 'warning',
        title: 'Warning!',
        text: message,
        iconColor: '#F59E0B',
        background: '#FFFBEB',
        color: '#92400E'
    });
};

// Confirmation dialog
window.showConfirm = function(options) {
    return Swal.fire({
        title: options.title || 'Are you sure?',
        text: options.text || 'This action cannot be undone!',
        icon: options.icon || 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: options.confirmButtonText || 'Yes, proceed!',
        cancelButtonText: 'Cancel',
        background: '#fff',
        color: '#333',
        customClass: {
            popup: 'colored-toast',
            title: 'text-lg font-medium',
            htmlContainer: 'text-base',
            confirmButton: 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
            cancelButton: 'px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2'
        }
    });
};

// Handle flash messages from Laravel
document.addEventListener('DOMContentLoaded', function() {
    // Success message
    if (window.successMessage) {
        showSuccess(window.successMessage);
    }
    
    // Error message
    if (window.errorMessage) {
        showError(window.errorMessage);
    }
    
    // Info message
    if (window.infoMessage) {
        showInfo(window.infoMessage);
    }
    
    // Warning message
    if (window.warningMessage) {
        showWarning(window.warningMessage);
    }
});

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Initialize Toastify for notifications
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

// Function to show toast notification
function showToast(message, type = 'info') {
    const colors = {
        info: 'linear-gradient(to right, #00b09b, #96c93d)',
        success: 'linear-gradient(to right, #00b09b, #96c93d)',
        warning: 'linear-gradient(to right, #f6d365, #fda085)',
        error: 'linear-gradient(to right, #ff5f6d, #ffc371)'
    };

    Toastify({
        text: message,
        duration: 5000,
        gravity: 'top',
        position: 'right',
        backgroundColor: colors[type],
        stopOnFocus: true,
        onClick: function() {}
    }).showToast();
}

// Listen for document forwarding notifications
if (window.userId) {
    window.Echo.private(`document-notifications.${window.userId}`)
        .listen('.document.forwarded', (e) => {
            const { document, notification } = e;
            
            // Show toast notification
            showToast(notification.message, 'info');

            // Update notification count in the bell icon
            const countElement = document.querySelector('#notification-count');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent || '0');
                countElement.textContent = currentCount + 1;
                countElement.style.display = 'flex';
            }

            // Add notification to the dropdown list if it exists
            const notificationsList = document.querySelector('#notifications-list');
            if (notificationsList) {
                const notificationHtml = `
                    <div class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer bg-blue-50 dark:bg-gray-700">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-alt text-blue-500"></i>
                            </div>
                            <div class="ml-3 w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${notification.title}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${notification.message}</p>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">${new Date().toLocaleString()}</p>
                            </div>
                        </div>
                    </div>
                `;
                notificationsList.insertAdjacentHTML('afterbegin', notificationHtml);
            }
        });
}

// // Handle success messages from Laravel
// if (window.successMessage) {
//     showToast(window.successMessage, 'success');
// }

// // Handle error messages from Laravel
// if (window.errorMessage) {
//     showToast(window.errorMessage, 'error');
// }

// // Handle warning messages from Laravel
// if (window.warningMessage) {
//     showToast(window.warningMessage, 'warning');
// }

// // Handle info messages from Laravel
// if (window.infoMessage) {
//     showToast(window.infoMessage, 'info');
// } 