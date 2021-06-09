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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Post-install code for badge ladder plugin
 *
 * @package    local_bs_badge_ladder
 * @copyright  2015 onwards Matthias Schwabe {@link http://matthiasschwa.be}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Code run after the bs_badge_ladder module database tables has been created.
 *
 * @return bool
 */
function xmldb_local_bs_badge_ladder_install() {
    global $DB;

    $courses = $DB->get_records('course', null, '', 'id');

    foreach ($courses as $course) {

        $record = new stdClass();
        $record->courseid = $course->id;
        $record->status = 0;
        $record->anonymize = 0;
        $record->perpage = 20;
        $DB->insert_record('local_badge_ladder', $record, false);
    }

    return true;
}
