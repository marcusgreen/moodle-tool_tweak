# Moodle Page tweak tool plugin by Marcus Green
[![Moodle Plugin CI](https://github.com/marcusgreen/moodle-tool_tweak/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/marcusgreen/moodle-tool_tweak/actions/workflows/moodle-ci.yml) [![GitHub Release](https://img.shields.io/github/release/marcusgreen/moodle-tool_tweak.svg)](https://github.com/marcusgreen//moodle-tool_tweak/releases)
[![Moodle Support](https://img.shields.io/badge/Moodle-%3E%3D%204.0-blue)](https://github.com/marcusgreen/moodle-tool_tweak/actions)


Page tweaks, make minor alterations to what is displayed based
on cohort membership, plugin tag or pagetype.

The home of this plugin is https://github.com/marcusgreen/moodle-tool_tweak

Documentation can be found at 

https://github.com/marcusgreen/moodle-tool_tweak/wiki

Site admins can create tweaks containing javascript and css
and which page types they can apply to. The pagetype is taken from
the global $PAGE variable. This script was hugely influenced by the
work of Dominique Bauer who has created a huge range of  javscript code to do things
such as modify how Moodle quiz questions appear. You can see his work at https://dynamiccourseware.org/. These tweaks will not work with the Mobile app as it uses
a different way to manage output.

Teachers can cause the tweak to be applied by adding a tag in the settings,e.g
in the tags for a quiz.


![alt text](./docs/images/settings.png "The settings page")


## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/admin/tool/tweak

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.
