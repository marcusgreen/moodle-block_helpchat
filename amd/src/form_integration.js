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
 * Moodle form integration for DOM parser
 *
 * @module     block_helpchat/form_integration
 * @copyright  2025 ISB Bayern
 * @author     Dr. Peter Mayer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {populateHiddenField, autoPopulateHiddenField } from './dom_parser';

/**
 * Initialize form integration with DOM parser
 *
 * @param {string} hiddenFieldId - ID of the hidden field to populate
 * @param {Object} options - Configuration options for DOM parser
 * @param {number} debounceMs - Debounce time for auto-population (default: 1000ms)
 */
export const initFormIntegration = (hiddenFieldId, options = {}, debounceMs = 1000) => {
    // Ensure DOM parser is available
    if (typeof window.MoodleDomParser === 'undefined') {
        console.warn('DOM Parser not available, loading...');
        // Try to load it dynamically
     import('./dom_parser.js').then(({ makeGloballyAccessible }) => {
            makeGloballyAccessible();
            setupIntegration(hiddenFieldId, options, debounceMs);
        }).catch(error => {
            console.error('Failed to load DOM parser:', error);
        });
    } else {
        setupIntegration(hiddenFieldId, options, debounceMs);
    }
};

/**
 * Setup the actual integration
 *
 * @param {string} hiddenFieldId
 * @param {Object} options
 * @param {number} debounceMs
 */
const setupIntegration = async (hiddenFieldId, options, debounceMs) => {
    try {
        // Initial population when form is ready
        await populateHiddenField(hiddenFieldId, options);
        console.log('Form integration initialized successfully');

        // Setup auto-population for dynamic updates
        const cleanup = autoPopulateHiddenField(hiddenFieldId, options, debounceMs);

        // Store cleanup function for later use
        window.M.block_helpchat.cleanupFormIntegration = cleanup;

        // Handle form submission
        const form = document.querySelector('form.moodleform');
        if (form) {
            form.addEventListener('submit', async (e) => {
                try {
                    // Ensure hidden field is populated before submission
                    await populateHiddenField(hiddenFieldId, options);
                } catch (error) {
                    console.error('Failed to populate hidden field before submission:', error);
                    // Don't prevent submission, just log the error
                }
            });
        }

    } catch (error) {
        console.error('Form integration setup failed:', error);
    }
};

/**
 * Manual trigger to populate form analysis data
 *
 * @param {string} hiddenFieldId
 * @param {Object} options
 */
export const populateFormAnalysis = async (hiddenFieldId, options = {}) => {
    try {
        if (typeof window.MoodleDomParser === 'undefined') {
            throw new Error('DOM Parser not available');
        }

        await populateHiddenField(hiddenFieldId, options);
        console.log('Form analysis data populated manually');

        return true;
    } catch (error) {
        console.error('Manual form analysis failed:', error);
        return false;
    }
};

/**
 * Get current form analysis data
 *
 * @param {string} hiddenFieldId
 * @returns {Object|null} Parsed form analysis data or null if not available
 */
export const getCurrentFormAnalysis = (hiddenFieldId) => {
    const hiddenField = document.getElementById(hiddenFieldId);
    if (!hiddenField || !hiddenField.value) {
        return null;
    }

    try {
        return JSON.parse(hiddenField.value);
    } catch (error) {
        console.error('Failed to parse form analysis data:', error);
        return null;
    }
};

/**
 * Get summary of current form state
 *
 * @param {string} hiddenFieldId
 * @returns {Object|null} Form summary or null if not available
 */
export const getFormSummary = (hiddenFieldId) => {
    const analysis = getCurrentFormAnalysis(hiddenFieldId);
    if (!analysis || !analysis.elements) {
        return null;
    }

    const elements = analysis.elements;

    return {
        timestamp: analysis.timestamp,
        totalElements: elements.length,
        filledElements: elements.filter(el => el.current_value && el.current_value.trim() !== '').length,
        visibleElements: elements.filter(el => el.visible === 1).length,
        hiddenElements: elements.filter(el => el.visible === 0).length,
        formUrl: analysis.formUrl,
        elementTypes: {
            text: elements.filter(el => ['text', 'email', 'password'].includes(el.type)).length,
            select: elements.filter(el => el.type === 'select').length,
            textarea: elements.filter(el => el.type === 'textarea').length,
            checkbox: elements.filter(el => el.type === 'checkbox').length,
            radio: elements.filter(el => el.type === 'radio').length
        }
    };
};

// Initialize Moodle namespace if it doesn't exist
if (typeof M.block_helpchat === 'undefined') {
    M.block_helpchat = {};
}

// Export functions to Moodle namespace
M.block_helpchat.initFormIntegration = initFormIntegration;
M.block_helpchat.populateFormAnalysis = populateFormAnalysis;
M.block_helpchat.getCurrentFormAnalysis = getCurrentFormAnalysis;
M.block_helpchat.getFormSummary = getFormSummary;
