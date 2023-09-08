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
 *
 * Edit themes form
 *
 * This does the same as the standard xml import but easier
 * @package    tool_tweak
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir.'/formslib.php');
require_once(__DIR__.'../../lib.php');
require_once("$CFG->dirroot/cohort/lib.php");

use tool_tweak\import_form;

$page   = optional_param('page', 0, PARAM_INT);
$newrecord = optional_param('newrecord', '', PARAM_TEXT);
$save = optional_param('save', '', PARAM_TEXT);
$delete = optional_param('delete', '', PARAM_TEXT);

$jsonsubmit = optional_param('jsonsubmit', '', PARAM_TEXT);
$savejson = optional_param('savejson', '', PARAM_TEXT);
$import = optional_param('import', '', PARAM_TEXT);
$export = optional_param('export', '', PARAM_TEXT);
$exportall = optional_param('exportall', '', PARAM_TEXT);

$context = context_system::instance();
$PAGE->set_context($context);
admin_externalpage_setup('tool_tweak_edit');

/**
 *  Edit tool_tweak code
 *
 * @copyright Marcus Green 2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * Form for editing tweak (css and javascript)
 */
class tool_tweak_edit_form extends moodleform {
    /**
     * Undocumented variable
     *
     * @var array
     */
    public $pagetypes = [];

    /**
     * Interface elements of the editing form.
     */
    protected function definition() {
        global $PAGE;
        $mform = $this->_form;
        // Add the popup CSS hints on pressing ctrl space.
        $PAGE->requires->css('/admin/tool/tweak/amd/src/codemirror/lib/codemirror.css');

        $PAGE->requires->css('/admin/tool/tweak/amd/src/codemirror/addon/hint/show-hint.css');
        $PAGE->requires->js_call_amd('tool_tweak/tweak_edit', 'init');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $navbuttons = [];
        $navbuttons[] = $mform->createElement('submit', 'save', get_string('save'));
        $navbuttons[] = $mform->createElement('submit', 'cancel', get_string('cancel'));
        $navbuttons[] = $mform->createElement('submit', 'newrecord', get_string('new'));
        $navbuttons[] = $mform->createElement('submit', 'delete', get_string('delete'));

        $mform->addGroup($navbuttons);

        $mform->addElement('header', 'importexportheader', get_string('tweakedit:importexportheader', 'tool_tweak'));
        $mform->addHelpButton('importexportheader', 'tweakedit:importexportheader', 'tool_tweak');

        $mform->setExpanded('importexportheader', false);

        $mform->addElement('static', 'importform', '', '<a href=import.php><div class="btn btn-primary">'
            .get_string('tweakedit:import', 'tool_tweak').'</div></a>');

        $mform->addElement('submit', 'export', get_string('tweakedit:export', 'tool_tweak'));
        $mform->addHelpButton('export', 'tweakedit:export', 'tool_tweak');

        $mform->addElement('submit', 'exportall', get_string('tweakedit:exportall', 'tool_tweak'));
        $mform->addHelpButton('exportall', 'tweakedit:exportall', 'tool_tweak');

        $mform->addElement('header', 'editheader', get_string('tweakedit:editheader', 'tool_tweak'));

        $mform->addElement('text', 'tweakname', get_string('name'));
        $mform->setType('tweakname', PARAM_TEXT);
        $mform->addHelpButton('tweakname', 'tweakedit:name', 'tool_tweak');
        $mform->addRule('tweakname',  get_string('tweakedit:name_required', 'tool_tweak'), 'required', '', 'server');

        $mform->addElement('advcheckbox', 'disabled', get_string('tweakedit:disabled', 'tool_tweak'), ' ');
        $mform->addHelpButton('disabled', 'tweakedit:disabled', 'tool_tweak');

        $options['multiple'] = true;
        $options['tags'] = true;

        $pagetypes = array_map('trim', explode(',', get_config('tool_tweak', 'pagetypes')));
        $pagetypes = array_combine($pagetypes, $pagetypes);
        $mform->addElement('autocomplete', 'pagetypes', get_string('tweakedit:pagetype', 'tool_tweak') , $pagetypes, $options);
        $mform->addHelpButton('pagetypes', 'tweakedit:pagetype', 'tool_tweak');

        $mform->addElement('text', 'tag', get_string('tag'));
        $mform->setType('tag', PARAM_TEXT);
        $mform->addHelpButton('tag', 'tweakedit:tag', 'tool_tweak');

        $fields = profile_get_custom_fields(true);
        $profilefields[''] = '';
        foreach ($fields as $field) {
            if ($field->datatype === 'checkbox') {
                $profilefields[$field->shortname] = $field->name;
            }
        }

        $mform->addElement('select', 'profilefield', get_string('tweakedit:profilefield', 'tool_tweak') , $profilefields);
        $mform->setType('profilefield', PARAM_TEXT);
        $mform->addHelpButton('profilefield', 'tweakedit:profilefield', 'tool_tweak');

        $allcohorts = cohort_get_all_cohorts();
        $cohorts[''] = ''; ;

        foreach ($allcohorts['cohorts'] as $cohort) {
            $cohorts[$cohort->id] = $cohort->name;
        }

        $mform->addElement('select', 'cohort', 'Cohort', $cohorts);
        $mform->addHelpButton('cohort', 'tweakedit:cohort', 'tool_tweak');
        $mform->setType('cohort', PARAM_RAW);

        $mform->addElement('textarea', 'css', get_string('tweakedit:css', 'tool_tweak'), ['rows' => 15, 'cols' => 80]);
        $mform->addHelpButton('css', 'tweakedit:css', 'tool_tweak');
        $mform->setType('css', PARAM_RAW);

        $mform->addElement('textarea', 'javascript', get_string('tweakedit:javascript', 'tool_tweak'),
         ['rows' => 15, 'cols' => 80]);
        $mform->addHelpButton('javascript', 'tweakedit:javascript', 'tool_tweak');
        $mform->setType('javascript', PARAM_RAW);

        $mform->addElement('textarea', 'html', get_string('tweakedit:html', 'tool_tweak'), ['rows' => 10, 'cols' => 80]);
        $mform->addHelpButton('html', 'tweakedit:html', 'tool_tweak');
        $mform->setType('html', PARAM_RAW);

    }
    /**
     * Sets the data before the form is displayed
     *
     * @param Object $tweak
     * @return void
     */
    public function set_data($tweak) {
        $this->_form->getElement('id')->setValue($tweak->id);
        $this->_form->getElement('tweakname')->setValue($tweak->tweakname ?? "");
        $this->_form->getElement('cohort')->setValue($tweak->cohort ?? "");
        $this->_form->getElement('tag')->setValue($tweak->tag ?? "");
        $this->_form->getElement('disabled')->setValue($tweak->disabled ?? "");
        $this->_form->getElement('css')->setValue($tweak->css ?? "");
        $this->_form->getElement('javascript')->setValue($tweak->javascript ?? "");
        $this->_form->getElement('html')->setValue($tweak->html ?? "");
        $this->_form->getElement('pagetypes')->setValue($tweak->pagetypes);
    }
}
$importform = new  import_form();
if ($data = $importform->get_data()) {
    $content = $importform->get_file_content('jsonfile');
    tool_tweak_import_json($content);
}
$recordcount = $DB->count_records('tool_tweak');

if ($recordcount == 0 || $newrecord) {
    $id = $DB->insert_record('tool_tweak', (object) ['tweakname' => '', 'cohort' => '', 'css' => '']);
    $record = $DB->get_record('tool_tweak', ['id' => $id]);
    $page = $DB->count_records('tool_tweak');
    $page --;
}

$recordcount = $DB->count_records('tool_tweak');

$record = get_page_record($page);

if ($delete ) {
    $DB->delete_records('tool_tweak', ['id' => $record->id]);
    $DB->delete_records('tool_tweak_pagetype', ['tweak' => $record->id]);
    $page--;
    $record = get_page_record($page);
    $recordcount = $DB->count_records('tool_tweak');
    if ($recordcount == 0) {
        $id = $DB->insert_record('tool_tweak', (object) ['tweakname' => '', 'cohort' => '', 'css' => '']);
        $record = $DB->get_record('tool_tweak', ['id' => $id]);
        $recordcount = 1;
    }
}
$baseurl = new moodle_url('/admin/tool/tweak/db/tweak_edit.php', ['page' => $page]);

$record->page = $page;

$mform = new tool_tweak_edit_form($baseurl);

if ($data = $mform->get_data()) {
    if (isset($data->save)) {
            $params = [
                'id' => $data->id,
                'tweakname' => $data->tweakname,
                'cohort' => $data->cohort,
                'tag' => $data->tag,
                'disabled' => $data->disabled,
                'profilefield' => $data->profilefield,
                'css' => $data->css,
                'javascript' => $data->javascript,
                'html' => $data->html
            ];
            $DB->update_record('tool_tweak', $params);
            update_pagetypes($data);
            $record = $DB->get_record('tool_tweak', ['id' => $data->id]);
    }
    if (isset($data->upload)) {
        $upload = true;
    }
    if (isset($data->export)) {
        do_download($data->id);
    }
    if (isset($data->exportall)) {
        do_download();
    }
}

$record = get_pagetypes($record);

$mform->set_data($record);

echo $OUTPUT->header();
if ($import) {
    $importform = new  import_form();
    $importform->display();
} else {
    echo $OUTPUT->paging_bar($recordcount, $page, 1, $baseurl);
    $mform->display();
}
if ($export) {
    do_download($data);
}
if (!$export) {
    echo $OUTPUT->footer();
}


/**
 * Get the database record to match the current page
 *
 * @param int $page
 * @return \stdClass
 */
function get_page_record(int $page) : \stdClass {
    global $DB;
    $record = (object) [];
    $recordset = $DB->get_recordset('tool_tweak');
    $count = 0;
    foreach ($recordset as $key => $value) {
        if ($count == $page) {
            $record = $value;
            break;
        }
        $count++;
    }
    return $record;
}
/**
 * If no page types given, delete any existing ones
 * Otherwise insert the ones given
 * @param mixed $data
 * @return void
 */
function  update_pagetypes($data) {
    global $DB;
    if (!$data->pagetypes) {
        $DB->delete_records('tool_tweak_pagetype', ['tweak' => $data->id]);
        return;
    }
    $DB->delete_records('tool_tweak_pagetype', ['tweak' => $data->id]);
    foreach ($data->pagetypes as $pagetype) {
         $DB->insert_record('tool_tweak_pagetype', ['tweak' => $data->id, 'pagetype' => $pagetype]);
    }
}

/**
 * Get the pagetypes (the tweak will act on) for a given
 * tweak record
 *
 * @param stdClass $record
 * @return stdClass
 */
function get_pagetypes(stdClass $record) {
    global $DB;
    $pagetypes = $DB->get_records_menu('tool_tweak_pagetype', ['tweak' => $record->id], '', 'id, pagetype');
    $pagetypes = array_combine($pagetypes, $pagetypes);
    if (count($pagetypes) > 0 ) {
        $record->pagetypes = $pagetypes;
    } else {
        $record->pagetypes = [];
    }
    return $record;
}
/**
 * Down load the tweak related to the given id
 * in json format so it can be re-used
 * @param int|null $id
 * @return never
 */
function do_download(int $id = null) {
    global $DB;
    $filename = '';
    if ($id) {
        $data = $DB->get_records('tool_tweak', ['id' => $id]);
        $filename = reset($data)->tweakname;
    } else {
         $data = $DB->get_records('tool_tweak');
         $filename = 'tweaks';
    }
    $json = '[';
    $recordcount = count($data);
    foreach ($data as $key => $record) {
        $text['tweakname'] = $record->tweakname;
        $text['tag'] = $record->tag;
        $text['cohort'] = $record->cohort;
        $text['disabled'] = $record->disabled;
        $text['profilefield'] = $record->profilefield;
        $text['javascript'] = $record->javascript;
        $text['css'] = $record->css;
        $text['html'] = $record->html;
        $text['pagetype'] = $DB->get_records_menu('tool_tweak_pagetype', ['tweak' => $record->id], null, 'id,pagetype');
        $json .= json_encode($text, JSON_PRETTY_PRINT);
        if ($key < $recordcount) {
            $json .= ',';
        }
    }
    $json .= ']';

    $filename .= '.json';
    header('Content-disposition: attachment; filename=' . $filename);
    header('Content-Type: application/json; charset: utf-8');
    echo $json;
    exit;
}
