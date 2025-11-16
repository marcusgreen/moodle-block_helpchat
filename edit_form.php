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
 * Form for editing Help Chat block instances.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Require the base block form class
require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing Help Chat block instances.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_helpchat_edit_form extends block_edit_form {

    /**
     * Extends the configuration form for the block.
     *
     * @param MoodleQuickForm $mform The form being built
     */
    protected function specific_definition($mform) {
        // Section header title.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Prompt field.
        $mform->addElement('textarea', 'config_prompt', get_string('prompt', 'block_helpchat'),
            array('rows' => 5, 'cols' => 50));
        $mform->setType('config_prompt', PARAM_TEXT);
        $mform->setDefault('config_prompt', get_string('defaultprompt', 'block_helpchat'));
        $mform->addHelpButton('config_prompt', 'prompt', 'block_helpchat');
    }
}
