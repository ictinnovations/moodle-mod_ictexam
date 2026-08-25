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
 * English language strings for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'ICTEXAM';
$string['modulename'] = 'ICTEXAM assessment';
$string['modulenameplural'] = 'ICTEXAM assessments';
$string['pluginadministration'] = 'ICTEXAM administration';
$string['modulename_help'] = 'The ICTEXAM activity launches an AI-graded assessment hosted on the ICTEXAM platform over LTI 1.3 and returns grades to the Moodle gradebook.';

$string['settings'] = 'ICTEXAM settings';
$string['tooltype'] = 'LTI tool';
$string['tooltype_help'] = 'Select the preconfigured ICTEXAM LTI 1.3 external tool. Configure it under Site administration > Plugins > External tool, with AGS and NRPS services enabled.';
$string['choosetooltype'] = 'Choose an LTI tool...';
$string['assessmentid'] = 'ICTEXAM assessment ID (optional)';
$string['assessmentid_help'] = 'Optional. The numeric assessment_id of the published paper in ICTEXAM to bind to this activity. Leave this blank to choose the paper inside ICTEXAM the first time you (as the teacher) open the activity — you will be shown a list of your papers to pick from, and your choice is linked to this activity for students.';
$string['classname'] = 'Class';
$string['classname_help'] = 'Class name sent to ICTEXAM as a custom LTI parameter (custom_class).';
$string['subjectname'] = 'Subject';
$string['subjectname_help'] = 'Subject name sent to ICTEXAM as a custom LTI parameter (custom_subject). Moodle has no native subject concept.';
$string['maximumgrade'] = 'Maximum grade';

$string['launch'] = 'Open assessment';
$string['notconfigured'] = 'This ICTEXAM activity has not been linked to an LTI tool yet. Please edit the activity settings.';

$string['ictexam:addinstance'] = 'Add a new ICTEXAM activity';
$string['ictexam:view'] = 'View ICTEXAM activity';
$string['ictexam:gradeexternal'] = 'Record ICTEXAM grades via web service';

$string['privacy:metadata:ictexam'] = 'The ICTEXAM activity sends user data to the external ICTEXAM assessment service so the exam can be delivered and graded, and so grades can be returned to this Moodle site.';
$string['privacy:metadata:ictexam:userid'] = 'The Moodle user ID of the person opening the activity, sent so the attempt and its grade can be matched back to the correct user.';
$string['privacy:metadata:ictexam:fullname'] = 'The full name of the person opening the activity, sent so their attempt can be identified in the ICTEXAM gradebook.';
$string['privacy:metadata:ictexam:email'] = 'The email address of the person opening the activity, sent as part of the LTI launch.';
$string['privacy:metadata:ictexam:grade'] = 'The score awarded for the attempt, returned by ICTEXAM and recorded in the Moodle gradebook.';
