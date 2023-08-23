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
 * Set default values for a new install
 *
 * @package     tool_tweak
 * @category    admin
 * @copyright   2023 Marcus Green
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {
    $ADMIN->add('tools', new admin_category('tweakcategory', get_string('pluginname', 'tool_tweak')));
    $settingspage = new admin_settingpage('tweaksettings' , get_string('settings:tweaksettings', 'tool_tweak'));
    $ADMIN->add('tweakcategory', $settingspage);

    $settingspage->add(new admin_setting_configtextarea('tool_tweak/pagetypes',
        get_string('settings:pagetypes', 'tool_tweak'),
        get_string('settings:pagetypes_text', 'tool_tweak'),
        "mod-quiz-attempt, mod-quiz-review",
         PARAM_RAW, 20, 3));

    $settingspage->add(new admin_setting_configcheckbox('tool_tweak/showpagetype',
         get_string('settings:showpagetype', 'tool_tweak'),
         get_string('settings:showpagetype_text', 'tool_tweak') , 0));
    $settingspage->add(new admin_setting_configcheckbox('tool_tweak/disablecache',
         get_string('settings:disablecache', 'tool_tweak'),
         get_string('settings:disablecache_text', 'tool_tweak') , 0));

    $ADMIN->add('tweakcategory',
        new admin_externalpage(
                'tool_tweak_edit',
                    get_string('tweakedit', 'tool_tweak'),
                    new moodle_url('/admin/tool/tweak/db/tweak_edit.php'),
                'moodle/site:config'
        ));
}
