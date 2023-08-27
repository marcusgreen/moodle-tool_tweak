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
 * Unit tests for tool_tweak lib
 *
 * @package    tool_tweak
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_tweak;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once(__DIR__.'/../lib.php');

/**
 * Test tool_tweak functions
 */
class lib_test extends \advanced_testcase {
    /**
     * Parse javascript for get_strings like php
     * @covers ::php_get_string()
     * @return void
     */
    public function test_php_get_string() {
        $content = "get_string('pluginname', 'tool_tweak)";
        $lib = new lib();
        $returnstring = $lib->php_get_string($content);
        $this->assertEquals("Page tweak", $returnstring);
    }
    /**
     * Parse javascript for get_strings like php
     * @covers ::import_json()
     * @return void
     */
    public function test_get_pagetype() {
        global $DB;
        $this->resetAfterTest();
        $count = $DB->count_records('tool_tweak');
        // Two records inserted from tweaks.json on install.
        $this->assertEquals(3, $count);
        $DB->delete_records('tool_tweak');

         $tweak[] = (Object) [
            'tweakname' => 'Test tweak',
            'tag' => 'test tag',
            'cohort' => '',
            'profilefiled' => '',
            'disabled' => 0,
            'javascript' => 'alert("hello world")',
            'CSS' => 'some css',
            'html' => '<h1>Hello</h1>',
            'pagetype' => ['mod-quiz-attempt', 'question-bank'],
         ];
         $json = json_encode($tweak, JSON_PRETTY_PRINT);
         tool_tweak_import_json($json);
         $count = $DB->count_records('tool_tweak');
         $this->assertEquals(1, $count);

    }
}
