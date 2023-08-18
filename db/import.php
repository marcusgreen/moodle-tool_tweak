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

require_once('../../../../config.php');
require_login();
global $CFG;
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__.'../../lib.php');

use tool_tweak\import_form;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/tweak/import.php'));

$importform = new  import_form();
if ($data = $importform->get_data()) {
    if (isset($data->cancel)) {
        redirect(new moodle_url('/admin/tool/tweak/db/tweak_edit.php'));
    }
    if (isset($data->save)) {
        $content = $importform->get_file_content('jsonfile');
        $recordcount = import_json($content);
        $msg = $recordcount. 'records imported';
        \core\notification::add($msg, \core\notification::WARNING);

    }
}
echo $OUTPUT->header();
$importform->display();
echo $OUTPUT->footer();

