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
 * Help Chat block settings.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $options = [
        'core_ai_subsystem' => 'Core AI Subsystem',
        'local_ai_manager' => 'Local AI Manager',
        'tool_aimanager' => 'Tool AI Manager'
    ];

    $settings->add(new admin_setting_configselect(
        'block_helpchat/backend',
        get_string('aibackend', 'block_helpchat'),
        get_string('aibackend_desc', 'block_helpchat'),
        'core_ai_subsystem',
        $options
    ));

    $settings->add(new admin_setting_configtextarea(
        'block_helpchat/prompt',
        get_string('prompt', 'block_helpchat'),
        get_string('prompt_desc', 'block_helpchat'),
        get_string('defaultprompt', 'block_helpchat'),
        PARAM_TEXT
    ));

}