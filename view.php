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
 * Launches the bound ICTEXAM assessment over LTI 1.3.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/lti/lib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$id = optional_param('id', 0, PARAM_INT);          // Course module id.
$n  = optional_param('n', 0, PARAM_INT);           // ictexam instance id.

if ($id) {
    $cm = get_coursemodule_from_id('ictexam', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $ictexam = $DB->get_record('ictexam', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    $ictexam = $DB->get_record('ictexam', ['id' => $n], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $ictexam->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('ictexam', $ictexam->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/ictexam:view', $context);

$event = \mod_ictexam\event\course_module_viewed::create([
    'objectid' => $ictexam->id,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('ictexam', $ictexam);
$event->trigger();

$PAGE->set_url('/mod/ictexam/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($ictexam->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if (empty($ictexam->toolurlid)) {
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('notconfigured', 'mod_ictexam'), 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// Build a synthetic LTI instance that core mod_lti can launch.
$type = $DB->get_record('lti_types', ['id' => $ictexam->toolurlid], '*', MUST_EXIST);

$customparams = [];
// Identify the activity + the launching Moodle user so the ICTEXAM backend can
// write the grade back into THIS activity's grade item (via mod_ictexam_set_grade)
// instead of creating a standalone AGS "Manual Item". $USER is the launching user
// (the student for a student launch), so this is the right grade recipient.
$customparams[] = 'cmid=' . $cm->id;
$customparams[] = 'moodle_user_id=' . $USER->id;
// Where to send the learner when they finish, so the ICTEXAM app can show a
// reliable "Return to Moodle" link after submission and on unlinked activities
// (QA #9/#34). The embedded synthetic launch does not always carry a
// launch_presentation.return_url claim, so pass it explicitly as a custom param.
$customparams[] = 'return_url=' . (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false);
// Multi-tenancy: on an IOMAD Moodle, one Moodle (one LTI issuer) hosts many
// customer "companies". Tag the launch with the launching user's company id so a
// single shared ICTEXAM backend can isolate each company's data on the
// (issuer, company) tenant key. Guarded by class_exists so a plain (non-IOMAD)
// Moodle is completely unaffected (no company param -> issuer-only tenant).
if (class_exists('\\iomad')) {
    try {
        $companyid = \iomad::get_my_companyid(context_system::instance(), false);
        if (!empty($companyid)) {
            $customparams[] = 'company=' . $companyid;
        }
    } catch (\Throwable $e) {
        // Non-fatal: proceed without a company tag.
    }
}
if (!empty($ictexam->assessmentid)) {
    $customparams[] = 'assessment_id=' . $ictexam->assessmentid;
}
if (!empty($ictexam->classname)) {
    $customparams[] = 'class=' . $ictexam->classname;
}
if (!empty($ictexam->subjectname)) {
    $customparams[] = 'subject=' . $ictexam->subjectname;
}

$instance = new stdClass();
$instance->id = $ictexam->id;
$instance->course = $course->id;
$instance->name = $ictexam->name;
$instance->cmid = $cm->id;
$instance->typeid = $type->id;
$instance->toolurl = '';
$instance->securetoolurl = '';
$instance->custom = new stdClass();
$instance->instructorcustomparameters = implode("\n", $customparams);
$instance->instructorchoiceacceptgrades = LTI_SETTING_ALWAYS;
$instance->instructorchoicesendname = LTI_SETTING_ALWAYS;
$instance->instructorchoicesendemailaddr = LTI_SETTING_ALWAYS;
$instance->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED;
$instance->debuglaunch = 0;
$instance->resourcekey = '';
$instance->password = '';
$instance->servicesalt = isset($ictexam->servicesalt) ? $ictexam->servicesalt : uniqid('', true);

// Hand off to the core LTI launcher (renders the OIDC auto-submit form).
lti_launch_tool($instance);
