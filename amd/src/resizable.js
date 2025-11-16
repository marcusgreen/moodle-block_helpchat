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
 * Resizable functionality for Help Chat block
 *
 * @module     block_helpchat/resizable
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialize resizable functionality for helpchat container
 */
export const initResizable = () => {
    // Find the helpchat container
    const container = document.querySelector('.helpchat-container');
    if (!container) {
        return;
    }
    
    // Make container resizable via CSS
    container.style.resize = 'both';
    container.style.overflow = 'auto';
    
    // Add resize handle
    const resizeHandle = document.createElement('div');
    resizeHandle.className = 'helpchat-resize-handle';
    container.appendChild(resizeHandle);
    
    // Store original dimensions in data attributes
    if (!container.dataset.originalWidth) {
        container.dataset.originalWidth = container.style.width || '100%';
    }
    if (!container.dataset.originalHeight) {
        container.dataset.originalHeight = container.style.height || '300px';
    }
    
    // Add event listener for mouse down on resize handle
    resizeHandle.addEventListener('mousedown', (e) => {
        e.preventDefault();
        const startX = e.clientX;
        const startY = e.clientY;
        const startWidth = parseInt(document.defaultView.getComputedStyle(container).width, 10);
        const startHeight = parseInt(document.defaultView.getComputedStyle(container).height, 10);
        
        // Add event listeners for mouse move and mouse up
        const doDrag = (e) => {
            container.style.width = (startWidth + e.clientX - startX) + 'px';
            container.style.height = (startHeight + e.clientY - startY) + 'px';
        };
        
        const stopDrag = () => {
            document.removeEventListener('mousemove', doDrag);
            document.removeEventListener('mouseup', stopDrag);
        };
        
        document.addEventListener('mousemove', doDrag);
        document.addEventListener('mouseup', stopDrag);
    });
};

/**
 * Reset container to original dimensions
 */
export const resetSize = () => {
    const container = document.querySelector('.helpchat-container');
    if (!container) {
        return;
    }
    
    container.style.width = container.dataset.originalWidth || '100%';
    container.style.height = container.dataset.originalHeight || '300px';
};