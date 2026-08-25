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
 * Web service function definitions for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_ictexam_set_grade' => [
        'classname'    => 'mod_ictexam\\external\\set_grade',
        'methodname'   => 'execute',
        'description'  => 'Record a score for a user against an ICTEXAM activity grade item.',
        'type'         => 'write',
        'capabilities' => 'mod/ictexam:gradeexternal',
        'ajax'         => false,
    ],
];

$services = [
    'ICTEXAM grade writeback' => [
        'functions'       => ['mod_ictexam_set_grade'],
        'restrictedusers' => 1,
        'enabled'         => 1,
        'shortname'       => 'mod_ictexam_grade',
        'downloadfiles'   => 0,
        'uploadfiles'     => 0,
    ],
];
