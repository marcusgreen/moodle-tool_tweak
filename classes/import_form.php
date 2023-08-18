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

namespace tool_tweak;

use coding_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");
/**
 * @package tool_tweak
 *
 * Import tweaks in Json format and insert into the database.
 */
class import_form extends \moodleform {
    /**
     * The interface elements of the form
     *
     * @return void
     * @throws coding_exception
     */
    protected function definition() {
        $mform = $this->_form;
        $maxbytes = 2048;

        $mform->addElement(
            'filepicker',
            'jsonfile',
            get_string('tweakedit:upload', 'tool_tweak'),
            null,
            [
                'maxbytes' => $maxbytes,
                'accepted_types' => '*',
            ]
        );
        $navbuttons[] = $mform->createElement('submit', 'save', get_string('save'));
        $navbuttons[] = $mform->createElement('submit', 'cancel', get_string('cancel'));

        $mform->addGroup($navbuttons);
    }
}
