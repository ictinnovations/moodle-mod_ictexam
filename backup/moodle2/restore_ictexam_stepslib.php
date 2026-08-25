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
 * Restore structure step for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete ictexam structure for restore.
 */
class restore_ictexam_activity_structure_step extends restore_activity_structure_step {

    /**
     * @return mixed
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('ictexam', '/activity/ictexam');

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process one ictexam instance.
     *
     * @param array|stdClass $data
     */
    protected function process_ictexam($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();
        $data->timecreated = !empty($data->timecreated) ? $this->apply_date_offset($data->timecreated) : time();
        $data->timemodified = !empty($data->timemodified) ? $this->apply_date_offset($data->timemodified) : time();

        $newitemid = $DB->insert_record('ictexam', $data);

        // Immediately after inserting the record, wire the coursemodule to it.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Re-link intro files into the new module context.
     */
    protected function after_execute() {
        $this->add_related_files('mod_ictexam', 'intro', null);
    }
}
