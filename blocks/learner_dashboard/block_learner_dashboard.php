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
 * Course list block.
 *
 * @package    block_course_list
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



class block_learner_dashboard extends block_list {
    function init() {
        $this->title = get_string('header', 'block_learner_dashboard');
    }

    function has_config() {
        return true;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';
        $html = '';
        $html .= '<div class="row">
                    <div class="col-md-3">
                      <div class="card-counter primary">
                        <i class="fa fa-briefcase"></i>
                        <span class="count-numbers">4</span>
                        <span class="count-name">Enrolled Courses</span>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="card-counter danger">
                        <i class="fa fa-graduation-cap"></i>
                        <span class="count-numbers">0</span>
                        <span class="count-name">Completed Courses</span>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="card-counter success">
                        <i class="fa fa-check"></i>
                        <span class="count-numbers">2</span>
                        <span class="count-name">Quizzes To Do</span>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="card-counter info">
                        <i class="fa fa-trophy"></i>
                        <span class="count-numbers">3</span>
                        <span class="count-name">Earned Badges</span>
                      </div>
                    </div>
                  </div>';
	$this->content->items[] = $html;
        
        return $this->content;
    }

    
}


