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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Activity settings form for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_ictexam_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        $mform->addElement('header', 'ictexamsettings', get_string('settings', 'mod_ictexam'));

        // The configured site-level LTI 1.3 tool type that points at the ICTEXAM backend.
        $tooltypes = $this->get_lti_tool_types();
        $mform->addElement('select', 'toolurlid', get_string('tooltype', 'mod_ictexam'), $tooltypes);
        $mform->addHelpButton('toolurlid', 'tooltype', 'mod_ictexam');

        $mform->addElement('text', 'assessmentid', get_string('assessmentid', 'mod_ictexam'), ['size' => '32']);
        $mform->setType('assessmentid', PARAM_INT);
        $mform->addHelpButton('assessmentid', 'assessmentid', 'mod_ictexam');

        $mform->addElement('text', 'classname', get_string('classname', 'mod_ictexam'), ['size' => '48']);
        $mform->setType('classname', PARAM_TEXT);
        $mform->addHelpButton('classname', 'classname', 'mod_ictexam');

        $mform->addElement('text', 'subjectname', get_string('subjectname', 'mod_ictexam'), ['size' => '48']);
        $mform->setType('subjectname', PARAM_TEXT);
        $mform->addHelpButton('subjectname', 'subjectname', 'mod_ictexam');

        $mform->addElement('text', 'grade', get_string('maximumgrade', 'mod_ictexam'), ['size' => '8']);
        $mform->setType('grade', PARAM_INT);
        $mform->setDefault('grade', 100);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Build a menu of configured LTI 1.3 tool types available on the site/course.
     *
     * @return array id => name
     */
    private function get_lti_tool_types() {
        global $DB, $COURSE;

        $options = [0 => get_string('choosetooltype', 'mod_ictexam')];
        if (!$DB->get_manager()->table_exists('lti_types')) {
            return $options;
        }
        $records = $DB->get_records_select(
            'lti_types',
            'state = ? AND (course = ? OR course = ?)',
            [1, SITEID, $COURSE->id],
            'name ASC',
            'id, name'
        );
        foreach ($records as $rec) {
            $options[$rec->id] = format_string($rec->name);
        }
        return $options;
    }
}
