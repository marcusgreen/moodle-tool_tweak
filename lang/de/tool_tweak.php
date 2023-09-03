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
 * Plugin strings are defined here.
 *
 * @package     tool_tweak
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['cachedef_tweakdata'] = 'Cachedef for  tweakdata';
$string['pluginname'] = 'Page tweak';
$string['tweakedit:disabled'] = 'Disabled';
$string['tweakedit:disabled_help'] = 'When this tweak is disabled the output will not be displayed.';

//$string['tweakedit:import'] = 'Importieren'; //  --> Button not impolemented in the same way (no translation possible).
//$string['tweakedit:import_help'] = 'Hilfe für [Importieren]';

$string['tweakedit:export'] = 'Exportieren';
$string['tweakedit:export_help'] = 'Hilfe für [Exportieren]';

$string['tweakedit:exportall'] = 'Alle exportieren';
$string['tweakedit:exportall_help'] = 'Hilfe für [Alle exportieren]';


$string['tweakedit'] = 'Tweak edit';
$string['settings:showpagetype'] = 'Show pagetypes';
$string['settings:showpagetype_text'] = 'The pagetype will be output in the page end if set and the user is an admin. For debug/development';

$string['settings:pagetypes'] = 'Pagetypes';
$string['settings:tweaksettings'] = 'Tweak config settings';
$string['settings:pagetypes_text'] = 'Comma separated list of pagetypes that can be used';

$string['settings:disablecache'] = 'Disable cache';
$string['settings:disablecache_text'] = 'Disable cache for when testing, so changes appear after saving';

$string['tweakedit:name'] = 'Name';
$string['tweakedit:importexportheader'] = 'Tweaks importieren/exportieren';
$string['tweakedit:importexportheader_help'] = 'Tweaks können im json-Format importiert oder exportiert werden. Es stehen zwei Export-Möglichkeitne zur Verfügung 1) [Exportieren] für den aktuell angezeigten Tweak und 2) [Alle exportieren] für alle Tweaks aus dem Plugin.';

$string['tweakedit:editheader'] = 'Tweaks bearbeiten';

$string['tweakedit:name_help'] = 'Hilfe für Name';
$string['tweakedit:name_required'] = 'der Name darf nicht leer sein';
$string['tweakedit:upload'] = 'Eine JSON-Datei hochladen ...';
$string['tweakedit:tag'] = 'Tag';
$string['tweakedit:tag_help'] = 'Der Tag wurde zu einem Kurs-Modul hinzugefügt';
$string['tweakedit:cohort'] = 'Cohort';
$string['tweakedit:cohort_help'] = 'If any cohorts (site wide groups) exist they will show up here. If a cohort is selected the tweak will be filtered out for anyone who is not a member';
$string['tweakedit:pagetype'] = 'Pagetype';
$string['tweakedit:pagetype_help'] = 'Pagetype name used internally by Moodle. If the tweak setting showpagetype is set, the pagetype will be shown at the end of each page to admin users.';
$string['tweakedit:css'] = 'CSS, Ctrl-space for hints';
$string['tweakedit:css_help'] = 'CSS without opening and clozing style tags. Ctrl-space will show popup help with syntax';
$string['tweakedit:javascript'] = 'Javascript';
$string['tweakedit:javascript_help'] = 'Javascript withoutout opening and closing script tags. calls to get_string will be converted as if this was PHP';
$string['tweakedit:html'] = 'HTML';
$string['tweakedit:profilefield'] = 'Profile field';
$string['tweakedit:profilefield_help'] = 'Shortname of a checkbox user profile field. Admins can add/modify from /user/profile/index.php';
$string['tweakedit:html_help'] = 'HTML put here is output before any javascript in the tweak. Put links to Content delivery systems (CDN\'s) here';
$string['cachedef_tweak'] = 'Description of the tweak data cache';
$string['returntoediting'] = 'Zurück zur Bearbeitung';
