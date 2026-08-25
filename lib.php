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
 * Core library functions and callbacks for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

/**
 * Declare which features the module supports.
 *
 * @param string $feature FEATURE_xxx constant.
 * @return mixed
 */
function ictexam_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        // The activity owns ONE gradebook column (itemtype=mod itemmodule=ictexam),
        // named after the activity. The ICTEXAM backend pushes scores into THIS item
        // via the mod_ictexam_set_grade web service (see classes/external/set_grade).
        // For mod_ictexam's synthetic LTI launch the backend deliberately does NOT
        // also create an AGS lineitem, so there is no second column — avoiding the
        // historical duplicate-column bug that motivated dropping this feature.
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;
        default:
            return null;
    }
}

/**
 * Create a new ictexam instance.
 *
 * @param stdClass $data form data
 * @param mod_ictexam_mod_form|null $mform
 * @return int new instance id
 */
function ictexam_add_instance($data, $mform = null) {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = time();
    if (!isset($data->intro)) {
        $data->intro = '';
    }
    if (!isset($data->introformat)) {
        $data->introformat = FORMAT_HTML;
    }

    $data->id = $DB->insert_record('ictexam', $data);

    // Create the activity's gradebook column up front so it appears even before
    // the first score is pushed back.
    ictexam_grade_item_update($data);

    return $data->id;
}

/**
 * Update an existing ictexam instance.
 *
 * @param stdClass $data form data
 * @param mod_ictexam_mod_form|null $mform
 * @return bool
 */
function ictexam_update_instance($data, $mform = null) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('ictexam', $data);

    // Keep the grade item (name / max grade) in sync with the edited settings.
    ictexam_grade_item_update($data);

    return true;
}

/**
 * Delete an ictexam instance.
 *
 * @param int $id instance id
 * @return bool
 */
function ictexam_delete_instance($id) {
    global $DB;

    $instance = $DB->get_record('ictexam', ['id' => $id]);
    if (!$instance) {
        return false;
    }

    $DB->delete_records('ictexam', ['id' => $id]);

    // Remove the activity's gradebook column.
    grade_update('mod/ictexam', $instance->course, 'mod', 'ictexam', $id, 0, null, ['deleted' => 1]);

    return true;
}

/**
 * Create or update the grade item for the given ICTEXAM instance.
 *
 * The ICTEXAM platform pushes scores into this item via the
 * mod_ictexam_set_grade web service; Moodle stores no per-user grade itself.
 *
 * @param stdClass $ictexam instance object with id, course, name and grade (max)
 * @param array|string|null $grades a single grade array, array of grades, or 'reset'
 * @return int GRADE_UPDATE_OK etc.
 */
function ictexam_grade_item_update($ictexam, $grades = null) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    $params = ['itemname' => $ictexam->name];
    if (isset($ictexam->grade) && (int)$ictexam->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = (int)$ictexam->grade;
        $params['grademin']  = 0;
    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/ictexam', $ictexam->course, 'mod', 'ictexam', $ictexam->id, 0, $grades, $params);
}

/**
 * Ensure the grade item exists. Called by Moodle during grade recalculation.
 *
 * Deliberately pushes NO per-user values (and never nulls them): grades are
 * owned externally and written via the web service, so this only guarantees the
 * column is present.
 *
 * @param stdClass $ictexam instance object
 * @param int $userid unused
 * @param bool $nullifnone unused
 */
function ictexam_update_grades($ictexam, $userid = 0, $nullifnone = true) {
    ictexam_grade_item_update($ictexam);
}
