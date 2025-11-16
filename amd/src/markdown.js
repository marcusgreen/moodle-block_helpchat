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
 * Simple markdown processor for Help Chat block
 *
 * @module     block_helpchat/markdown
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Simple markdown to HTML converter
 * Supports basic markdown syntax:
 * - Headers (#, ##, ###)
 * - Bold (**text** or __text__)
 * - Italic (*text* or _text_)
 * - Lists (- item)
 * - Links ([text](url))
 * - Code blocks (```code```)
 * - Line breaks
 *
 * @param {string} markdown - The markdown text to convert
 * @returns {string} The HTML representation
 */
export const renderMarkdown = (markdown) => {
    if (!markdown) {
        return '';
    }

    let html = markdown;

    // Convert code blocks (```code```)
    html = html.replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>');

    // Convert inline code (`code`)
    html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

    // Convert headers (# Header)
    html = html.replace(/^### (.*$)/gim, '<h3>$1</h3>');
    html = html.replace(/^## (.*$)/gim, '<h2>$1</h2>');
    html = html.replace(/^# (.*$)/gim, '<h1>$1</h1>');

    // Convert bold (**text** or __text__)
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/__(.*?)__/g, '<strong>$1</strong>');

    // Convert italic (*text* or _text_)
    html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');
    html = html.replace(/_(.*?)_/g, '<em>$1</em>');

    // Convert links ([text](url))
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');

    // Convert unordered lists (- item)
    html = html.replace(/^\- (.*$)/gim, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>)/gim, '<ul>$1</ul>');

    // Convert line breaks
    html = html.replace(/\n/g, '<br>');

    return html;
};

/**
 * Process markdown in helpchat response
 */
export const processResponseMarkdown = () => {
    // Find all helpchat response containers
    const responseContainers = document.querySelectorAll('.helpchat-response-content');
    
    responseContainers.forEach(container => {
        // Get the text content
        const markdownText = container.textContent || container.innerText;
        
        // Convert markdown to HTML
        const htmlContent = renderMarkdown(markdownText);
        
        // Update the container with HTML content
        container.innerHTML = htmlContent;
    });
};

/**
 * Initialize markdown processing for helpchat responses
 */
export const initMarkdown = () => {
    // Process markdown when the page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', processResponseMarkdown);
    } else {
        processResponseMarkdown();
    }
    
    // Also process markdown after form submission
    const form = document.getElementById('helpchat-form');
    if (form) {
        form.addEventListener('submit', () => {
            // Process markdown after a short delay to allow for content update
            setTimeout(processResponseMarkdown, 1000);
        });
    }
};