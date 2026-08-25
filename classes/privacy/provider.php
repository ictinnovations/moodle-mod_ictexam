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
 * Privacy Subsystem implementation for mod_ictexam.
 *
 * @package    mod_ictexam
 * @copyright  2026 ICT Innovations
 * @author     Tahir Almas <tahir@ictinnovations.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_ictexam\privacy;

use core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider for mod_ictexam.
 *
 * The plugin stores no personal data of its own — the ictexam table holds activity
 * configuration only, and grades live in the core gradebook. It does, however, send
 * the launching user's identity to the external ICTEXAM service, so that transmission
 * is declared here as an external location.
 */
class provider implements \core_privacy\local\metadata\provider {

    /**
     * Describe the personal data mod_ictexam sends to the external ICTEXAM service.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link(
            'ictexam',
            [
                'userid' => 'privacy:metadata:ictexam:userid',
                'fullname' => 'privacy:metadata:ictexam:fullname',
                'email' => 'privacy:metadata:ictexam:email',
                'grade' => 'privacy:metadata:ictexam:grade',
            ],
            'privacy:metadata:ictexam'
        );

        return $collection;
    }
}
