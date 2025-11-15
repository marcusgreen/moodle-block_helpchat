<?php
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
 * Help Chat form.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_helpchat\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

// Ensure CFG is available
global $CFG;
if (empty($CFG)) {
    throw new Exception('Moodle $CFG not available');
}

if (empty($CFG->libdir)) {
    throw new Exception('Moodle $CFG->libdir not available');
}

require_once($CFG->libdir . '/formslib.php');

// Debug: Log that the class file is being loaded
// error_log('Loading helpchat_form class');

/**
 * Help Chat form class.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helpchat_form extends moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        // Hidden field for context information
        $mform->addElement('hidden', 'form_analysis_data', '');
        $mform->setType('form_analysis_data', PARAM_RAW);

        // Check if we're in question editing context
        $isquestionediting = isset($this->_customdata['isquestionediting']) ? $this->_customdata['isquestionediting'] : false;

        // Add question editing prompt if in question editing context
        if ($isquestionediting) {
            $mform->addElement('static', 'questioneditingprompt', '', 
                '<div class="helpchat-context-info" style="background-color: #e7f3ff; border-left: 4px solid #0066cc; padding: 8px; margin-bottom: 10px; font-size: 0.9em;">
                    <strong>Question Editing Mode:</strong> You can ask for help with question quality, clarity, educational value, and alignment with learning objectives.
                </div>');
        }

        // Message textarea
        $mform->addElement('textarea', 'helpchat_message', '', [
            'placeholder' => get_string('messageplaceholder', 'block_helpchat'),
            'class' => 'helpchat-textarea',
            'rows' => 5
        ]);
        $mform->setType('helpchat_message', PARAM_TEXT);
        $mform->addRule('helpchat_message', null, 'required', null, 'client');
        $mform->addRule('helpchat_message', null, 'minlength', 1, 'client');

        // Submit button
        $buttonarray = [];
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('submitbutton', 'block_helpchat'));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
    }

    /**
     * Validation function.
     *
     * @param array $data Array of form data
     * @param array $files Array of form files
     * @return array Array of errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate message length
        if (!empty($data['helpchat_message']) && strlen(trim($data['helpchat_message'])) < 1) {
            $errors['helpchat_message'] = get_string('err_emptymessage', 'block_helpchat');
        }

        // Check for maximum length (e.g., 2000 characters)
        if (!empty($data['helpchat_message']) && strlen(trim($data['helpchat_message'])) > 2000) {
            $errors['helpchat_message'] = get_string('err_messagetoolong', 'block_helpchat', ['max' => 2000]);
        }

        return $errors;
    }

    /**
     * Set form data for display.
     *
     * @param array $data Data to set
     */
    public function set_data($data) {
        // Ensure we have the analysis data if provided
        if (!isset($data['form_analysis_data'])) {
            $data['form_analysis_data'] = '';
        }
        parent::set_data($data);
    }
    
    /**
     * Render the form.
     *
     * @return string HTML for the form
     */
    public function render() {
        ob_start();
        $this->display();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}