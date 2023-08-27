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
require_once($CFG->dirroot.'/user/profile/lib.php');

/**
 * Class lib
 *
 * @package    tool_tweak
 * @copyright  2023 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib {
    /**
     * If a tweak has a cohort but the current user is not in that cohort
     * remove the tweak from alltweaks.
     * @param array $tweaks
     * @return array
     */
    public function filter_by_cohort(array $tweaks) :array {
        global $DB, $USER;
        $cache = \cache::make('tool_tweak', 'tweakdata');
        if (($usercohorts = $cache->get('usercohorts')) === false) {
            $usercohorts = $DB->get_records_menu('cohort_members', ['userid' => $USER->id], 'id,userid');
            $cache->set('usercohorts', $usercohorts);
        }
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
     * Take the current pagetype and loop
     * through alltweaks. Unset any
     * that do not have this pagetype.
     *
     * @param array $tweaks
     * @return array
     */
    public function filter_by_pagetype(array $tweaks) : array {
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
     * Filter out tags that require tags not found
     * in the current module setup.
     * @param array $tweaks
     * @param int $cmid
     * @return array
     */
    public function filter_by_tag(array $tweaks, int $cmid) : array {
        $plugintags = $this->get_plugintags($cmid);
        foreach ($tweaks as $key => $tweak) {
            if ($tweak->tag) {
                if (!in_array($tweak->tag, $plugintags )) {
                    unset($tweaks[$key]);
                }
            }
        }
        return $tweaks;
    }
    /**
     * Filter out tweaks where the profilefield is not blank and that
     * is not checked (set to "1! for the user). Must be a checkbox
     * profile field type.
     * @param array $tweaks
     * @return array
     */
    public function filter_by_profilefield(array $tweaks) : array {
        global $USER;
        foreach ($tweaks as $key => $tweak) {
            if ($tweak->profilefield <> '') {
                if (array_key_exists($tweak->profilefield, $USER->profile )) {
                    if ($USER->profile[$tweak->profilefield] <> "1") {
                        unset($tweaks[$key]);
                    }
                }
            }
        }
        return $tweaks;
    }

    /**
     * Get all tweaks and associated page types
     * minus the actual content (html,css,javascript)
     *
     * @return array
     */
    public function get_all_tweaks() : array {
        global $DB;
        $sql = 'SELECT tweak.id, tweakname, cohort,tag,pagetype,disabled,profilefield FROM {tool_tweak} tweak
                LEFT JOIN {tool_tweak_pagetype} pagetype on pagetype.tweak=tweak.id
                WHERE tweak.disabled <> 1';
        $alltweaks = $DB->get_recordset_sql($sql);
        $tweaks = [];
        foreach ($alltweaks as $tweak) {
            $tweaks[] = $tweak;
        }
        return $tweaks;
    }

    /**
     * Get any tags set up for this plugin instance
     *
     * @param mixed $cmid
     * @return mixed
     */
    private function get_plugintags($cmid) {
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
    /**
     * Give javascript some of the Moodle core
     * get_string capability
     *
     * @param string $content
     * @return string
     */
    public function php_get_string(string $content) {
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
     * Get unique id values for all pagetypes currently stored
     * for this plugin
     *
     * @return array
     */
    public function get_distinct_pagetypes() : array {
        global $DB;
        $pagetypes = $DB->get_records_sql('SELECT DISTINCT pagetype FROM {tool_skin_pagetype}');
        return array_keys($pagetypes);
    }

}
