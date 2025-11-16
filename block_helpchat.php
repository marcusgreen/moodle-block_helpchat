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
 * Help Chat block.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Help Chat block class.
 *
 * @package    block_helpchat
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_helpchat extends block_base {

    /**
     * Initialize the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_helpchat');
    }

    /**
     * Get the content of the block.
     *
     * @return stdClass The content
     */
    public function get_content() {
        global $CFG, $USER, $COURSE, $PAGE;
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Detect if we're in a question editing context
        $is_question_editing = $this->is_question_editing_context();

        // Process form submission if there is one
        $response = '';
        $message = optional_param('helpchat_message', '', PARAM_TEXT);
        $formdata = optional_param('form_analysis_data', '', PARAM_TEXT);

        if (!empty($message)) {
            try {
                $response = $this->perform_request($message, 'helpchat', $is_question_editing);
            } catch (Exception $e) {
                $response = get_string('errorprocessingrequest', 'block_helpchat');
            }
        }

        // Prepare the data for the template
        $templatedata = [
            'messageplaceholder' => get_string('messageplaceholder', 'block_helpchat'),
            'submitbutton' => get_string('submitbutton', 'block_helpchat'),
            'response' => $response,
            'isquestionediting' => $is_question_editing
        ];

        // Render the Mustache template
        $this->content->text = $this->render_helpchat_form($templatedata);

        return $this->content;
    }
    /**
     * Render the helpchat form using a Mustache template.
     *
     * @param array $data The data to pass to the template
     * @return string The rendered HTML
     */
    protected function render_helpchat_form($data) {
         global $OUTPUT;
        // Make DOM parser globally accessible for console usage
        $this->page->requires->js_call_amd(
            'block_helpchat/dom_parser',
            'makeGloballyAccessible'
        );
        $this->page->requires->js_call_amd(
            'block_helpchat/form_integration',
            'initFormIntegration',
            ['form-analysis-data']
        );
        return $OUTPUT->render_from_template('block_helpchat/helpchat_form', $data);
    }

    /**
     * Perform a request to the LLM system.
     *
     * @param string $usermessage The user message to send to the LLM
     * @param string $purpose The purpose of the request
     * @param bool $is_question_editing Whether we're in a question editing context
     * @return string The response from the LLM
     * @throws moodle_exception
     */
    public function perform_request(string $usermessage, string $purpose = 'helpchat', bool $is_question_editing = false): string {
        global $CFG, $USER;

        if (defined('BEHAT_SITE_RUNNING') || (defined('PHPUNIT_TEST') && PHPUNIT_TEST)) {
            return "AI Response to: " . $usermessage;
        }

        // Get the prompt from block instance configuration
        $prompt = '';
        if (!empty($this->config->prompt)) {
            $prompt = $this->config->prompt;
        } else {
            // Use question editing prompt if in question editing context
            if ($is_question_editing) {
                $prompt = get_string('questioneditingprompt', 'block_helpchat');
            } else {
                // Fallback to global config prompt
                $globalprompt = get_config('block_helpchat', 'prompt');
                if (!empty($globalprompt)) {
                    $prompt = $globalprompt;
                }
            }
        }

        // Combine prompt with user message
        $fullprompt = '';
        if (!empty($prompt)) {
            $fullprompt = $prompt . "\n\n" . $usermessage;
        } else {
            $fullprompt = $usermessage;
        }

        // Try to get backend from config or use a default
        $backend = get_config('block_helpchat', 'backend');
        if (empty($backend)) {
            $backend = 'core_ai_subsystem'; // Default to core AI subsystem
        }

        if ($backend == 'local_ai_manager' && class_exists('local_ai_manager\manager')) {
            $manager = new local_ai_manager\manager($purpose);
            $llmresponse = (object) $manager->perform_request($fullprompt, 'block_helpchat', $this->context->id);
            if ($llmresponse->get_code() !== 200) {
                throw new moodle_exception(
                    'err_retrievingfeedback',
                    'block_helpchat',
                    '',
                    $llmresponse->get_errormessage(),
                    $llmresponse->get_debuginfo()
                );
            }
            return $llmresponse->get_content();
        } else if ($backend == 'core_ai_subsystem' && class_exists('core_ai\manager')) {
            $action = new \core_ai\aiactions\generate_text(
                contextid: $this->context->id,
                userid: $USER->id,
                prompttext: $fullprompt
            );
            $manager = \core\di::get(\core_ai\manager::class);
            $llmresponse = $manager->process_action($action);
            $responsedata = $llmresponse->get_response_data();
            // Check the response data is actually a string.
            if (
                is_null($responsedata) || is_null($responsedata['generatedcontent'])
                ||
                !is_array($responsedata) || !array_key_exists('generatedcontent', $responsedata)
            ) {
                if (is_null($responsedata) || is_null($responsedata['generatedcontent'])) {
                    throw new moodle_exception('err_retrievingfeedback_checkconfig', 'block_helpchat');
                } else {
                    throw new moodle_exception('err_retrievingfeedback', 'block_helpchat');
                }
            }
            return $responsedata['generatedcontent'];
        } else if ($backend == 'tool_aimanager' && class_exists('tool_aiconnect\ai\ai')) {
            $ai = new tool_aiconnect\ai\ai();
            $llmresponse = $ai->prompt_completion($fullprompt);
            return $llmresponse['response']['choices'][0]['message']['content'];
        }

        // Fallback to simple response if no backend is configured
        return "AI response to: " . $fullprompt;
    }

    /**
     * Check if we're in a question editing context.
     *
     * @return bool True if in question editing context
     */
    protected function is_question_editing_context() {
        global $PAGE;

        $pagetype = $PAGE->pagetype ?? '';

        // Check for question editing page types
        if (preg_match('/^question-/', $pagetype) ||
            preg_match('/^admin-/', $pagetype) ||
            strpos($pagetype, 'question') !== false) {
            return true;
        }

        // Check URL patterns that indicate question editing
        $url = $PAGE->url ?? null;
        if ($url && $url instanceof moodle_url) {
            $path = $url->get_path();
            if (strpos($path, '/question/edit') !== false ||
                strpos($path, '/question/bank') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Define where this block can be used.
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'site-index' => true,
            'course-view' => true,
            'my' => true,
            'page' => true,
            'question-*' => true,
            'admin-*' => true
        );
    }

    /**
     * Allow multiple instances of this block.
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Has instance configuration.
     *
     * @return bool
     */
    public function instance_allow_config() {
        return true;
    }
}