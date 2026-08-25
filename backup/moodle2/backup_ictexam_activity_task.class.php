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
 * Backup task for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/ictexam/backup/moodle2/backup_ictexam_stepslib.php');

/**
 * Backup task that provides all the settings and steps to back up mod_ictexam.
 */
class backup_ictexam_activity_task extends backup_activity_task {

    /**
     * No particular settings for this activity.
     */
    protected function define_my_settings() {
    }

    /**
     * Define (add) particular steps this activity can have.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_ictexam_activity_structure_step('ictexam_structure', 'ictexam.xml'));
    }

    /**
     * Encode absolute links to this module so they can be restored to a new id.
     *
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '#');

        // Link to the view of one ictexam: .../mod/ictexam/view.php?id=123.
        $search = '#(' . $base . '/mod/ictexam/view\.php\?id=)([0-9]+)#';
        $content = preg_replace($search, '$@ICTEXAMVIEWBYID*$2@$', $content);

        return $content;
    }
}
