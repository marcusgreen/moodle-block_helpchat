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
 * Strings for component 'block_helpchat', language 'en'.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Help Chat';
$string['helpchat:addinstance'] = 'Add a new Help Chat block';
$string['helpchat:myaddinstance'] = 'Add a Help Chat block to my dashboard';
$string['messageplaceholder'] = 'Type your message here...';
$string['submitbutton'] = 'Send Message';
$string['errorprocessingrequest'] = 'Error processing your request. Please try again.';
$string['err_retrievingfeedback'] = 'Error retrieving feedback from AI service: {$a}';
$string['err_retrievingfeedback_checkconfig'] = 'Error retrieving feedback from AI service. Please check configuration.';
$string['aibackend'] = 'AI Backend';
$string['aibackend_desc'] = 'Select which AI backend to use for processing requests.';
$string['prompt'] = 'System Prompt';
$string['prompt_help'] = 'This text will be prepended to all user messages before sending to the AI. Use this to provide context or instructions to the AI.';
$string['prompt_desc'] = 'This text will be prepended to all user messages before sending to the AI. Use this to provide context or instructions to the AI.';
$string['defaultprompt'] = 'Only answer questions relating to the Moodle stack question type or maths. Assume you are already \n
     in the question editing form.Be as accurate as possible. Use rand_with_step where possible for questions about random numbers.\n
     Don’t remind to use plaintext. When adding a second node question must be saved first. Populate model answers with pre-calculated\n
     variables if possible. When displaying the content of a variable e.g. X in the Question text use the style {@x@}';
$string['questioneditingprompt'] = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.';
$string['defaultpromptsetting'] = 'Default Prompt';
$string['defaultpromptsetting_desc'] = 'Prompt that each instance of a block is initialised with.';
$string['privacy:metadata'] = 'The Help Chat block does not store any personal data.';