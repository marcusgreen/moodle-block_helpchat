// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Spinner functionality for Help Chat block
 *
 * @module     block_helpchat/spinner
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Show spinner on submit button
 *
 * @param {HTMLElement} button - The submit button element
 */
export const showSpinner = (button) => {
    // Store original content
    button.dataset.originalContent = button.innerHTML;
    
    // Create spinner element
    const spinner = document.createElement('span');
    spinner.className = 'helpchat-spinner';
    spinner.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
    
    // Replace button content with spinner
    button.innerHTML = spinner.innerHTML;
    button.disabled = true;
};

/**
 * Hide spinner and restore button
 *
 * @param {HTMLElement} button - The submit button element
 */
export const hideSpinner = (button) => {
    // Restore original content if it was stored
    if (button.dataset.originalContent) {
        button.innerHTML = button.dataset.originalContent;
        button.disabled = false;
        delete button.dataset.originalContent;
    }
};

/**
 * Initialize spinner functionality for helpchat form
 */
export const initSpinner = () => {
    // Find the helpchat form
    const form = document.getElementById('helpchat-form');
    if (!form) {
        return;
    }
    
    // Find the submit button
    const submitButton = form.querySelector('.helpchat-submit');
    if (!submitButton) {
        return;
    }
    
    // Add submit event listener to show spinner
    form.addEventListener('submit', () => {
        showSpinner(submitButton);
    });
    
    // Hide spinner when page is fully loaded (form submission complete)
    window.addEventListener('load', () => {
        hideSpinner(submitButton);
    });
};