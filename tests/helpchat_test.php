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

namespace block_helpchat;

use advanced_testcase;
use block_helpchat;
use context_course;

/**
 * PHPUnit block_helpchat tests
 *
 * @package    block_helpchat
 * @category   test
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \block_helpchat
 */
final class block_helpchat_test extends advanced_testcase {
    public static function setUpBeforeClass(): void {
        require_once(__DIR__ . '/../../moodleblock.class.php');
        require_once(__DIR__ . '/../block_helpchat.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test the block title is set correctly.
     */
    public function test_block_title(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course and prepare the page where the block will be added.
        $course = $this->getDataGenerator()->create_course();
        $page = new \moodle_page();
        $page->set_context(context_course::instance($course->id));
        $page->set_pagelayout('course');

        $block = new block_helpchat();
        $block->init();
        
        // Test that the block title is set correctly
        $this->assertEquals('Help Chat', $block->title);
        
        // Verify the title comes from the language string
        $this->assertEquals(get_string('pluginname', 'block_helpchat'), $block->title);
    }

    /**
     * Test that the block can be added to question editing pages.
     */
    public function test_applicable_formats_include_question_editing(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $block = new block_helpchat();
        $applicable_formats = $block->applicable_formats();
        
        // Test that question editing formats are supported
        $this->assertTrue($applicable_formats['question-*']);
        $this->assertTrue($applicable_formats['admin-*']);
        $this->assertTrue($applicable_formats['page']);
        $this->assertTrue($applicable_formats['course-view']);
    }

    /**
     * Test question editing context detection.
     */
    public function test_question_editing_context_detection(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $block = new block_helpchat();
        
        // Mock page object for question editing context
        $mock_page = $this->createMock(stdClass::class);
        $mock_page->pagetype = 'question-type-multichoice';
        $mock_page->url = new \moodle_url('/question/edit.php');
        
        // Replace global PAGE temporarily
        global $PAGE;
        $original_page = $PAGE;
        $PAGE = $mock_page;
        
        try {
            // Test reflection to access private method
            $reflection = new \ReflectionClass($block);
            $method = $reflection->getMethod('is_question_editing_context');
            $method->setAccessible(true);
            
            $result = $method->invoke($block);
            $this->assertTrue($result);
        } finally {
            // Restore original PAGE
            $PAGE = $original_page;
        }
    }
}
