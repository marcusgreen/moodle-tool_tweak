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

function tool_tweak_before_footer() {
    global $DB;
    $cmid = optional_param('cmid', null, PARAM_INT);
    $id = optional_param('id', null , PARAM_INT);

    $cmid = $cmid ?? $id;
    show_pagetype();
    $cache = cache::make('tool_tweak', 'tweakdata');
    if (($tweaks = $cache->get('tweaks')) === false) {
        $tweaks = get_all_tweaks();
        $cache->set('tweaks', $tweaks);
    }

    $tweaks = filter_by_cohort($tweaks);
    $tweaks = filter_by_pagetype($tweaks);
    if ($cmid) {
        $tweaks = filter_by_tag($tweaks, $cmid);
    }
    $tweakids = [];
    foreach ($tweaks as $tweak) {
        $tweakids[] = $tweak->id;
    }
    if (count($tweakids)) {
        [$insql, $inparams] = $DB->get_in_or_equal($tweakids);
        $sql = "SELECT * FROM {tool_tweak} WHERE id $insql";
        $fulltweaks = $DB->get_records_sql($sql, $inparams);
    }

    $content = '';
    if ($fulltweaks) {
        foreach ($fulltweaks as $tweak) {
                     $record = $DB->get_record('tool_tweak', ['id' => $tweak->id]);
                     $content .= $record->html. PHP_EOL;
                     $content .= '<script>'.$record->javascript. '</script>'.PHP_EOL;
                     $content .= '<style>'.$record->css. '</style>'.PHP_EOL;
        }
    }

    $content = php_get_string($content);
    return $content;
}

/**
 * Get any tags set up for this plugin instance
 *
 * @param mixed $cmid
 * @return mixed
 */
function get_plugintags($cmid) {
    global $DB;
    $sql = "SELECT name as tagname
              FROM {tag_instance} ti
              JOIN {tag} tag
                ON ti.tagid=tag.id
               AND ti.itemtype='course_modules'
               AND ti.itemid = :cmid";

    $plugintags = $DB->get_records_sql($sql, ['cmid' => $cmid]);
    return $plugintags;
}

function filter_by_tag(array $tweaks, int $cmid) : array {
    $plugintags = get_plugintags($cmid);
    foreach ($tweaks as $key => $tweak) {
        if ($tweak->tag) {
            $i = 0;
            if (!in_array($tweak->tag, $plugintags )) {
                unset($tweaks[$key]);
            }
        }
    }
    return $tweaks;
}
/**
 * Take the current pagetype and loop
 * through alltweaks. Unset any
 * that do not have this pagetype.
 *
 * @param mixed $alltweaks
 * @return array
 */
function filter_by_pagetype(array $tweaks) : array {
    global $PAGE;
    $pagetype = $PAGE->pagetype;
    $parts = explode('-', $PAGE->pagetype);
    $plugintype = $parts[0].'-'.$parts[1];
    foreach ($tweaks as $key => $tweak) {
        if ($tweak->pagetype) {
            if (($tweak->pagetype !== $pagetype) && ($tweak->pagetype !== $plugintype)) {
                unset($tweaks[$key]);
            }
        }
    }
    return $tweaks;

}
/**
 * If a tweak has a cohort but the current user is not in that cohort
 * remove the tweak from alltweaks.
 * @param mixed $tweaks
 * @return array
 */
function filter_by_cohort(array $tweaks) :array {
    global $DB, $USER;
    $usercohorts = $DB->get_records_menu('cohort_members', ['userid' => $USER->id], 'id,userid');
    foreach ($tweaks as $key => $tweak) {
        if ($tweak->cohort > 0) {
            if (!in_array($tweak->cohort, $usercohorts)) {
                unset($tweaks[$key]);
            }

        }
    }
    return $tweaks;
}

/**
 * Get all tweaks and associated page types
 *
 * @return array
 */
function get_all_tweaks() : array {
    global $DB;
    $sql = 'SELECT tweak.id, tweakname, cohort,tag,pagetype FROM {tool_tweak} tweak
            LEFT JOIN {tool_tweak_pagetype} pagetype on pagetype.tweak=tweak.id';
    $alltweaks = $DB->get_recordset_sql($sql);
    $tweaks = [];
    foreach ($alltweaks as $tweak) {
        $tweaks[] = $tweak;
    }
    return $tweaks;
}

/**
 * Get unique id values for all pagetypes currently stored
 * for this plugin
 *
 * @return array
 */
function get_distinct_pagetypes() : array {
    global $DB;
    $pagetypes = $DB->get_records_sql('SELECT DISTINCT pagetype FROM {tool_skin_pagetype}');
    return array_keys($pagetypes);
}

/**
 * Give javascript some of the Moodle core
 * get_string capability
 *
 * @param string $content
 * @return string
 */
function php_get_string(string $content) {
    preg_match_all('/get_string\\(.*?\)/', $content, $matches);
    foreach ($matches[0] as $functioncall) {
        $toreplace = $functioncall;
        // Remove spaces and single quotes.
        $functioncall = str_replace([" ", "'"], "", $functioncall);
        // Get content between parentheseis.
        preg_match('/\((.*?)\)/', $functioncall, $matches);
        $params = explode(',', $matches[1]);
        if (count($params) == 1) {
            $string = get_string($params[0]);
        } else {
            $string = get_string($params[0], $params[1]);
        }
        $string = '"'.$string.'"';
        $content = str_replace($toreplace, $string, $content);
    }
    return trim($content, '"');
}

/**
 * Take in a json string, convert to an object
 * and write to the tables
 *
 * @param string $json
 * @return int
 * @todo Implement error trapping
 */
function import_json(string $json) : int {
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
function show_pagetype() : void {
    global $USER, $PAGE;
    if (get_config('tool_tweak', 'showpagetype')) {
        if (is_siteadmin($USER->id)) {
            $msg = 'page-type:'.$PAGE->pagetype;
            \core\notification::add($msg, \core\notification::WARNING);
        }
    }
}
