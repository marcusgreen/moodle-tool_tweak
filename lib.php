<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Utilities for admin/tool/tweak plugin for tweaking the UI.
 *
 * @package     tool_tweak
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Page callback to get any paagetype tweaks for this module instance.
 * @package     tool_tweak
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Output the css/html/javascript for the tweak that
 * match the filtered criteria.
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function tool_tweak_before_footer() {
    global $DB;
    $lib = new tool_tweak\lib();

    $cmid = optional_param('cmid', null, PARAM_INT);
    $id = optional_param('id', null , PARAM_INT);

    $cmid = $cmid ?? $id;
    tool_tweak_show_pagetype();
    $cache = cache::make('tool_tweak', 'tweakdata');
    if (($tweaks = $cache->get('tweaks')) === false) {
        $tweaks = $lib->get_all_tweaks();
        $cache->set('tweaks', $tweaks);
    }

    if (get_config('tool_tweak', 'disablecache')) {
        $tweaks = $lib->get_all_tweaks();
    }

    $tweaks = $lib->filter_by_cohort($tweaks);
    $tweaks = $lib->filter_by_pagetype($tweaks);
    if ($cmid) {
        $tweaks = $lib->filter_by_tag($tweaks, $cmid);
    }
    $tweakids = [];
    foreach ($tweaks as $tweak) {
        $tweakids[$tweak->id] = $tweak->id;
    }
    if (count($tweakids)) {
        [$insql, $inparams] = $DB->get_in_or_equal($tweakids);
        $sql = "SELECT * FROM {tool_tweak} WHERE id $insql";
        $fulltweaks = $DB->get_records_sql($sql, $inparams);
    }

    $content = '';
    if (isset($fulltweaks)) {
        foreach ($fulltweaks as $tweak) {
                     $content .= $tweak->html. PHP_EOL;
                     $content .= '<script>'.$tweak->javascript. '</script>'.PHP_EOL;
                     $content .= '<style>'.$tweak->css. '</style>'.PHP_EOL;
        }
    }

    $content = $lib->php_get_string($content);

    return $content;
}

/**
 * Take in a json string, convert to an object
 * and write to the tables
 *
 * @param string $json
 * @return int
 * @todo Implement error trapping
 */
function tool_tweak_import_json(string $json) : int {
    $jsonobject = json_decode($json, false);
    global $DB;
    $recordcount = 0;
    foreach ($jsonobject as $field) {
        $recordcount++;
        $pagetypes = $field->pagetype;
        unset($field->pagetype);
        $tweakid = $DB->insert_record('tool_tweak', $field);
        foreach ($pagetypes as $pagetype) {
            $DB->insert_record('tool_tweak_pagetype', ['tweak' => $tweakid, 'pagetype' => $pagetype]);
        }
    }
    return $recordcount;
}
/**
 * Show the page type to the admin user
 * Purely for debug and setup
 */
function tool_tweak_show_pagetype() : void {
    global $USER, $PAGE;
    if (get_config('tool_tweak', 'showpagetype')) {
        if (is_siteadmin($USER->id)) {
            $msg = 'page-type:'.$PAGE->pagetype;
            \core\notification::add($msg, \core\notification::WARNING);
        }
    }
}
