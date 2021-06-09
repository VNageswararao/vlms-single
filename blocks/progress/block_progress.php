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
 * Block for displaying earned local progress to users
 *
 * @package    block_progress
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Displays recent progress
 */
class block_progress extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_progress');
    }

    public function instance_allow_multiple() {
        return true;
    }

    public function has_config() {
        return false;
    }

    public function instance_allow_config() {
        return true;
    }

    public function applicable_formats() {
        return array(
                'admin' => false,
                'site-index' => true,
                'course-view' => true,
                'mod' => false,
                'my' => true
        );
    }

    public function specialization() {
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_progress');
        } else {
            $this->title = $this->config->title;
        }
    }

    public function get_content() {
        global $USER, $CFG;

        if ($this->content !== null || !user_has_role_assignment($USER->id, 5)) {
            return $this->content;
        }

        if (empty($this->config)) {
            $this->config = new stdClass();
        }

   
        // Create empty content.
        $this->content = new stdClass();
        $this->content->text = '';
        $currentDate=date('Y-m-d');
        $year=date('Y');
        $year=$year-1;
       $lastYearBegin=($year.'-01-01');
        //echo $lastYear = date("Y-m-d", strtotime("-1 years")); die;
        $quarters = get_quarters($lastYearBegin,$currentDate);
        $quarters=array_reverse($quarters);
        $quarter_wise_enrollments=[];
        foreach($quarters as $k=>$v){
        // $quarter_wise_enrollments[$v->period]['Enrolled']=get_enrolled_courses_count($v->period_start,$v->period_end); 
         $quarter_wise_enrollments[$v->period]=get_completed_courses_quarter_wise($v->period_start,$v->period_end); 
        }
        $completedCourses=array_values($quarter_wise_enrollments);
        //echo "<pre>";print_r($completedCourses);die;
        if(max($completedCourses)<5){
          $max=5;  
        }else{
         $max=max($completedCourses)+2;     
        }
        
        $yaxisData='["'.implode('","',array_keys($quarter_wise_enrollments)).'"]';     
        $xaxisData="[".implode(",",array_values($quarter_wise_enrollments))."]";
        if (array_sum($completedCourses)) {
           // $output = $this->page->get_renderer('core','progress');
            $this->content->text .= ' <canvas id="bar-chart1" style="width: 100%;height:350px;"></canvas> ';
        } else {
            $this->content->text .= get_string('nothingtodisplay', 'block_progress');
        }
        $this->content->footer.='<script src="'.$CFG->wwwroot.'/blocks/progress/js/chart.min.js"></script>';
        $this->content->footer.='<script type="text/javascript">
        new Chart(document.getElementById("bar-chart1"), {
            type: "horizontalBar",
            data: {
                labels:'.$yaxisData.',
                datasets: [
				  {
                      label: "Completed Courses",
                      backgroundColor: "rgba(1,109,209,0.7)",
                      borderColor: "rgba(0,88,170,0.7)",
                      borderWidth: 2,
                      hoverBackgroundColor: "rgba(1,109,209,0.9)",
                      hoverBorderColor: "rgba(0,88,170,0.7)",
                      data:'.$xaxisData.'
                  },
                  
                ]
            },
            options: {
                title: {
                    display: false,   
                  },
                  legend: {
                    display: false,
                  },
                scales: {
                    yAxes: [{
                    display: true,
                   
                    }],
                    xAxes: [{
                    display: true,
                    gridLines: {
                    display: false
                    },
                    ticks: {
                    beginAtZero: true,
                    stepSize: 1,
                    max:'.$max.'
                    }
                    },
                    
                    ]
                },
            }
        });
    </script>';
        return $this->content;
    }
}

function get_enrolled_courses_count($quarterSdate,$quarterEdate){
    global $DB;
    $coursesCount=0;
    $sql="SELECT COUNT(ue.enrolid) AS enrolCourseCount,e.courseid,c.shortname,u.username,e.roleid,ue.userid,
e.enrolperiod,e.enrolstartdate,e.enrolenddate,FROM_UNIXTIME(ue.timestart),FROM_UNIXTIME(ue.timeend)
FROM mdl_user_enrolments AS ue JOIN mdl_enrol AS e ON ue.enrolid=e.id JOIN mdl_course AS c 
ON c.id=e.courseid JOIN mdl_user AS u ON u.id=ue.userid WHERE ue.timestart BETWEEN $quarterSdate AND $quarterEdate";
    $count=$DB->get_record_sql($sql);
    if($count->enrolcoursecount){
        $coursesCount=$count->enrolcoursecount;
    }
    return $coursesCount;
}

function get_completed_courses_quarter_wise($quarterSdate,$quarterEdate){
  global $USER,$DB;
    $coursesCount=0;
    $sql="SELECT COUNT(ue.id) as completedcount,e.courseid,e.roleid,ue.userid,
e.enrolperiod,e.enrolstartdate,e.enrolenddate,
FROM_UNIXTIME(ue.timestart),FROM_UNIXTIME(ue.timeend) FROM mdl_user_enrolments AS ue 
JOIN mdl_enrol AS e ON ue.enrolid=e.id
JOIN mdl_course_completions AS cc  ON cc.userid=ue.userid AND ue.userid=$USER->id AND cc.course=e.courseid 
WHERE cc.timecompleted BETWEEN $quarterSdate AND $quarterEdate";
    $count=$DB->get_record_sql($sql);
    if($count->completedcount){
        $coursesCount=$count->completedcount;
    }
    return $coursesCount;   
}

function get_quarters($start_date, $end_date){
	
	$quarters = array();
	
	$start_month = date( 'm', strtotime($start_date) );
	$start_year = date( 'Y', strtotime($start_date) );
	
	$end_month = date( 'm', strtotime($end_date) );
	$end_year = date( 'Y', strtotime($end_date) );
	
	$start_quarter = ceil($start_month/3);
	$end_quarter = ceil($end_month/3);

	$quarter = $start_quarter; // variable to track current quarter
	
	// Loop over years and quarters to create array
	for( $y = $start_year; $y <= $end_year; $y++ ){
		if($y == $end_year)
			$max_qtr = $end_quarter;
		else
			$max_qtr = 4;
		
		for($q=$quarter; $q<=$max_qtr; $q++){
			
			$current_quarter = new stdClass();
			
			$end_month_num = zero_pad($q * 3);
			$start_month_num = ($end_month_num - 2);

			$q_start_month = month_name($start_month_num);
			$q_end_month = month_name($end_month_num);
			
			$current_quarter->period = "$q_start_month $y";
			$current_quarter->period_start = strtotime("$y-$start_month_num-01");      // yyyy-mm-dd    
			$current_quarter->period_end = strtotime("$y-$end_month_num-" . month_end_date($y, $end_month_num));
			
			$quarters[] = $current_quarter;
			unset($current_quarter);
		}

		$quarter = 1; // reset to 1 for next year
	}
	
	return $quarters;
	
}
// get month name from number
function month_name($month_number){
	return date('F', mktime(0, 0, 0, $month_number, 10));
}


// get get last date of given month (of year)
function month_end_date($year, $month_number){
	return date("t", strtotime("$year-$month_number-0"));
}

// return two digit month or day, e.g. 04 - April
function zero_pad($number){
	if($number < 10)
		return "0$number";
	
	return "$number";
}
