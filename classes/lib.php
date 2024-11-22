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

defined('MOODLE_INTERNAL') || die();

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
     * Summary of before_standard_head_html_generation
     * @param \core\hook\output\before_standard_head_html_generation $hook
     * @return void
     * @package tool_tweak
     */
    public static function before_standard_head_html_generation(
        \core\hook\output\before_standard_head_html_generation $hook,

    ): void {
        global $DB, $PAGE;
        $PAGE->requires->css('/admin/tool/tweak/amd/src/codemirror/lib/codemirror.css');
        $PAGE->requires->css('/admin/tool/tweak/amd/src/codemirror/addon/hint/show-hint.css');
    }

    /**
     *
     * Output items at the end of pages
     * @param \core\hook\output\before_standard_footer_html_generation $hook
     * @return void
     */
    public static function before_standard_footer_html_generation(
    \core\hook\output\before_standard_footer_html_generation $hook): void {
        global $DB;
        $cmid = optional_param('cmid', null, PARAM_INT);
        $id = optional_param('id', null , PARAM_INT);

        $cmid = $cmid ?? $id;
        self::show_pagetype();
        $cache = \cache::make('tool_tweak', 'tweakdata');
        if (($tweaks = $cache->get('tweaks')) === false) {
            $tweaks = self::get_all_tweaks();
            $cache->set('tweaks', $tweaks);
        }

        if (get_config('tool_tweak', 'disablecache')) {
            $tweaks = self::get_all_tweaks();
        }

        $tweaks = self::filter_by_cohort($tweaks);
        $tweaks = self::filter_by_pagetype($tweaks);
        $tweaks = self::filter_by_profilefield($tweaks);
        if ($cmid) {
            $tweaks = self::filter_by_tag($tweaks, $cmid);
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
                        $content .= '<script>var current_language="'.current_language().'";'
                        .PHP_EOL.$tweak->javascript. '</script>'.PHP_EOL;
                        $content .= '<style>'.$tweak->css. '</style>'.PHP_EOL;
            }
        }
        $content = self::php_get_string($content);
        global $PAGE;
        $PAGE->requires->js_call_amd('tool_tweak/edit_form', 'init', ['javascript' => 'id_questiontext']);

        $hook->add_html($content);

    }
    /**
     * If a tweak has a cohort but the current user is not in that cohort
     * remove the tweak from alltweaks.
     * @param array $tweaks
     * @return array
     */
    public static function filter_by_cohort(array $tweaks): array {
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
    public static function filter_by_pagetype(array $tweaks): array {
        global $PAGE;
        $pagetype = $PAGE->pagetype;
        $parts = explode('-', $PAGE->pagetype);
        if (count($parts) > 1) {
            $plugintype = $parts[0].'-'.$parts[1];
        } else {
            // Is this really correct?.
            $plugintype = $pagetype;
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
    public static function filter_by_tag(array $tweaks, int $cmid): array {
        $plugintags = self::get_plugintags($cmid);
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
    public static function filter_by_profilefield(array $tweaks): array {
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
    public static function get_all_tweaks(): array {
        global $DB;
        $sql = 'SELECT tweak.id, tweakname, cohort,tag,pagetype, disabled, profilefield FROM {tool_tweak} tweak
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
    private static function get_plugintags($cmid) {
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
    public static function php_get_string(string $content) {
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
     * for this plugin (is this actually needed?)
     *
     * @return array
     */
    public static function get_distinct_pagetypes(): array {
        global $DB;
        $pagetypes = $DB->get_records_sql('SELECT DISTINCT pagetype FROM {tool_tweak_pagetype}');
        return array_keys($pagetypes);
    }
    /**
     * Show the page type to the admin user
     * Purely for debug and setup doesn't work on some pages
     */
    public static function show_pagetype(): void {

        global $USER, $PAGE, $OUTPUT;
        if (get_config('tool_tweak', 'showpagetype')) {
            if (is_siteadmin($USER->id)) {
                $msg = 'page-type:'.$PAGE->pagetype;
                \core\notification::add($msg, \core\notification::WARNING);
            }
        }
    }

}
