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

/*
 * JavaScript to add popup css hints in the editor
 *
 * @package tool_tweak
 * @copyright 2023 Marcus Green
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import CodeMirror from 'tool_tweak/codemirror/lib/codemirror';
import 'tool_tweak/codemirror/addon/hint/show-hint';
import 'tool_tweak/codemirror/addon/hint/css-hint';
import 'tool_tweak/codemirror/addon/hint/javascript-hint';
import 'tool_tweak/codemirror/addon/hint/html-hint';

import 'tool_tweak/codemirror/mode/css/css';
import 'tool_tweak/codemirror/mode/javascript/javascript';
import 'tool_tweak/codemirror/mode/xml/xml';
import 'tool_tweak/codemirror/mode/markdown/markdown';
import 'tool_tweak/codemirror/mode/meta/meta';
import 'tool_tweak/codemirror/mode/htmlmixed/htmlmixed';

//        mode: {name: "javascript", globalVars: true},

export const init = () => {
    var editor = CodeMirror.fromTextArea(document.getElementById("id_css"), {
        lineNumbers: true,
        mode: 'text/css',
        extraKeys: {"Ctrl-Space": "autocomplete"}
          });
   editor.setSize('100%', 200);

   var editor = CodeMirror.fromTextArea(document.getElementById("id_javascript"), {
    lineNumbers: true,
    mode: {name: "javascript", globalVars: true},
    extraKeys: {"Ctrl-Space": "autocomplete"}
      });

   var editor = CodeMirror.fromTextArea(document.getElementById("id_html"), {
    lineNumbers: true,
    mode: {name: "htmlmixed"},
    extraKeys: {"Ctrl-Space": "autocomplete"}
      });
editor.setSize('100%', 200);
};
