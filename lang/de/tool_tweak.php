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

$string['cachedef_tweakdata'] = 'Cachedef für tweakdata';
$string['pluginname'] = 'Page tweak';
$string['tweakedit:disabled'] = 'Deaktiviert';
$string['tweakedit:disabled_help'] = 'Falls dieser Tweak deaktiviert ist, werden die unten erfassten Anpassungen nicht ausgeführt/angezeigt.';

$string['tweakedit:import'] = 'Importieren';
$string['tweakedit:import_help'] = 'Hilfe für Importieren';

$string['tweakedit:export'] = 'Exportieren';
$string['tweakedit:export_help'] = 'Hilfe für Exportieren';

$string['tweakedit:exportall'] = 'Alle exportieren';
$string['tweakedit:exportall_help'] = 'Hilfe für Alle exportieren';

$string['tweakedit'] = 'Tweak edit';
$string['settings:showpagetype'] = 'Pagetypes anzeigen';
$string['settings:showpagetype_text'] = 'Der pagetype wird zu Debugging-/Entwicklungszwecken allen Admin-Usern auf der Seite  angezeigt, falls aktiviert.';

$string['settings:pagetypes'] = 'Pagetypes';
$string['settings:tweaksettings'] = 'Einstellungen für Tweaks';
$string['settings:pagetypes_text'] = 'Durch Kommas getrennte Liste von pagetypes, für welche dieser Tweak aktiviert wird.';

$string['settings:disablecache'] = 'Cache deaktivieren';
$string['settings:disablecache_text'] = 'Cache während der Erstellung des Tweaks deaktivieren, damit Änderungen sofort sichtbar sind.';

$string['tweakedit:name'] = 'Name';
$string['tweakedit:importexportheader'] = 'Tweaks importieren/exportieren';
$string['tweakedit:importexportheader_help'] = 'Tweaks können im json-Format importiert oder exportiert werden. Es stehen zwei Export-Möglichkeitne zur Verfügung 1) [Exportieren] für den aktuell angezeigten Tweak und 2) [Alle exportieren] für alle Tweaks aus dem Plugin.';

$string['tweakedit:editheader'] = 'Tweaks bearbeiten';

$string['tweakedit:name_help'] = 'Hilfe für Name';
$string['tweakedit:name_required'] = 'der Name darf nicht leer sein';
$string['tweakedit:upload'] = 'Eine JSON-Datei hochladen ...';
$string['tweakedit:tag'] = 'Tag';
$string['tweakedit:tag_help'] = 'Der Tag wurde zu einem Kurs-Modul hinzugefügt';
$string['tweakedit:cohort'] = 'Globale Gruppen';
$string['tweakedit:cohort_help'] = 'Falls globale Gruppen erfasst sind (Website-Administration ->Nutzer/innen ->Nutzerkonten ->Globale Gruppen), werden diese hier angezeigt. Falls eine globale Gruppe ausgewählt ist, wird der Tweak nur dieser Gruppe angezeigt.';
$string['tweakedit:pagetype'] = 'Pagetype';
$string['tweakedit:pagetype_help'] = 'Moodle-interner pagetype Name. Der pagetype wird zu Debugging-/Entwicklungszwecken allen Admin-Usern am Ende jeder Seite angezeigt, falls pagetype anzeigen aktiviert ist.';
$string['tweakedit:css'] = 'CSS, Ctrl-Space um Tipps anzuzeigen';
$string['tweakedit:css_help'] = 'CSS ohne style-Tags. Ctrl-Space öffnet Pop-up-Hilfe mit Tipps';
$string['tweakedit:javascript'] = 'Javascript';
$string['tweakedit:javascript_help'] = 'Javascript ohne Script-Tags. Aufrufe von get_string werden wie in PHP umgewandelt.';
$string['tweakedit:html'] = 'HTML';
$string['tweakedit:profilefield'] = 'Profilfelder';
$string['tweakedit:profilefield_help'] = 'Shortname von selbst erstellten Checkbox-Profilfeldern. Admins können diese unter /user/profile/index.php hinzufügen und bearbeiten.';
$string['tweakedit:html_help'] = 'Der hier eingegebene HTML-Code wird vor allfälligem javascript-Code im Tweak ausgegeben. Links zu Content Delivery Systems (CDN\'s) hier einfügen.';
$string['cachedef_tweak'] = 'Beschreibung des Tweak-Data-Cache';
$string['returntoediting'] = 'Zurück zur Bearbeitung';