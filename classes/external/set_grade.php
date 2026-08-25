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
 * External web service function that records a score against the ICTEXAM activity grade item.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_ictexam\external;

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Lets the ICTEXAM backend push a score into the activity's own grade item
 * (itemtype=mod itemmodule=ictexam), so the grade appears under the Moodle
 * activity name rather than as a standalone AGS "Manual Item".
 */
class set_grade extends external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid'     => new external_value(PARAM_INT, 'Course module id of the ICTEXAM activity'),
            'userid'   => new external_value(PARAM_INT, 'Moodle user id to grade'),
            'score'    => new external_value(PARAM_FLOAT, 'Raw score achieved'),
            'scoremax' => new external_value(PARAM_FLOAT, 'Maximum possible raw score', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * @param int $cmid course module id
     * @param int $userid Moodle user id
     * @param float $score raw score
     * @param float $scoremax raw score maximum (0 = treat score as already scaled)
     * @return array
     */
    public static function execute($cmid, $userid, $score, $scoremax = 0): array {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/ictexam/lib.php');

        $params = self::validate_parameters(self::execute_parameters(), [
            'cmid'     => $cmid,
            'userid'   => $userid,
            'score'    => $score,
            'scoremax' => $scoremax,
        ]);

        $cm = get_coursemodule_from_id('ictexam', $params['cmid'], 0, false, MUST_EXIST);
        $context = \context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/ictexam:gradeexternal', $context);

        $ictexam = $DB->get_record('ictexam', ['id' => $cm->instance], '*', MUST_EXIST);

        // Store the student's ACTUAL marks, not a percentage. When the caller
        // gives the exam's raw maximum (scoremax), make the activity grade item
        // use that same maximum and record the raw score directly, so Moodle
        // shows e.g. 15/20 instead of the scaled-to-100 percentage (75). The new
        // maximum is persisted to the instance so later grade recalculations
        // (ictexam_update_grades -> ictexam_grade_item_update, which reads
        // $ictexam->grade) keep the same scale instead of resetting it.
        $smax = (float) $params['scoremax'];
        $raw = (float) $params['score'];

        if ($smax > 0) {
            $itemmax = $smax;
            $desiredmax = (int) round($smax);
            if ($desiredmax < 1) {
                $desiredmax = 1;
            }
            if ((int) $ictexam->grade !== $desiredmax) {
                $DB->set_field('ictexam', 'grade', $desiredmax, ['id' => $ictexam->id]);
                $ictexam->grade = $desiredmax;
            }
        } else {
            // Caller already scaled the score; grade against the existing max.
            $itemmax = (float) ((int) $ictexam->grade > 0 ? (int) $ictexam->grade : 100);
        }

        $stored = $raw;
        if ($stored < 0) {
            $stored = 0;
        }
        if ($stored > $itemmax) {
            $stored = $itemmax;
        }

        $result = ictexam_grade_item_update($ictexam, [
            'userid'   => $params['userid'],
            'rawgrade' => $stored,
        ]);

        return [
            'status'      => ($result === GRADE_UPDATE_OK),
            'scaledgrade' => $stored,
            'grademax'    => $itemmax,
        ];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status'      => new external_value(PARAM_BOOL, 'Whether the grade was recorded'),
            'scaledgrade' => new external_value(PARAM_FLOAT, 'Grade stored against the activity item'),
            'grademax'    => new external_value(PARAM_FLOAT, 'Activity grade item maximum'),
        ]);
    }
}
