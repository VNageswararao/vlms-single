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



class block_admin_coursestatus extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_admin_coursestatus');
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
       $this->content->items[] = '<div class="row card-line card-border">
                    <div class="col-md-4">
                      <div class="card">                       
                        <h4 class="count-courses">12</h4>
                        <span class="count-text">Courses</span>
                      </div>
                    </div> 
                     <div class="col-md-8">
                      <div class="">                        
                    
                        <span class="count-name">Courses with no Enrollments</span>
                      </div>
                    </div>
                  </div>';
        $this->content->items[] = '<div class="row card-line card-border">
                    <div class="col-md-4">
                      <div class="card">                       
                        <h4 class="count-courses">6</h4>
                        <span class="count-text">Courses</span>
                      </div>
                    </div> 
                     <div class="col-md-8">
                      <div class="">                        
                    
                        <span class="count-name">Courses with no Contents</span>
                      </div>
                    </div>
                  </div>';
         $this->content->items[] = '<div class="row card-line ">
                    <div class="col-md-4">
                      <div class="card">                       
                        <h4 class="count-courses">8</h4>
                        <span class="count-text">Courses</span>
                      </div>
                    </div> 
                     <div class="col-md-8">
                      <div class="">                        
                    
                        <span class="count-name">Courses with no Completion</span>
                      </div>
                    </div>
                  </div>';
	//$this->content->items[] = $html;
        return $this->content;
    }

    
}


