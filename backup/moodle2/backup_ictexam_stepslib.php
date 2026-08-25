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
 * Backup structure step for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete ictexam structure for backup, with file annotations.
 *
 * An ictexam instance is configuration only (it links to an ICTEXAM assessment
 * over LTI and lets AGS own the grades), so there is no per-user data to back up.
 */
class backup_ictexam_activity_structure_step extends backup_activity_structure_step {

    /**
     * @return backup_nested_element
     */
    protected function define_structure() {
        // The instance row. No userinfo branch — this module stores no user data.
        $ictexam = new backup_nested_element('ictexam', ['id'], [
            'course', 'name', 'intro', 'introformat', 'toolurlid',
            'assessmentid', 'classname', 'subjectname', 'grade',
            'timecreated', 'timemodified',
        ]);

        $ictexam->set_source_table('ictexam', ['id' => backup::VAR_ACTIVITYID]);

        // Intro images live in the standard module 'intro' file area.
        $ictexam->annotate_files('mod_ictexam', 'intro', null);

        return $this->prepare_activity_structure($ictexam);
    }
}
