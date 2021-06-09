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
 * A two column layout for the hurix theme.
 *
 * @package   theme_hurix
 * @copyright 2017 Willian Mano - http://conecti.me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use core_course\external\course_summary_exporter;
user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
user_preference_allow_ajax_update('sidepre-open', PARAM_ALPHA);

require_once($CFG->libdir . '/behat/lib.php');
global $USER;
if (isloggedin()) {
    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
    $draweropenright = (get_user_preferences('sidepre-open', 'true') == 'true');
} else {
    $navdraweropen = false;
    $draweropenright = false;
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = strpos($blockshtml, 'data-block=') !== false;

$extraclasses = [];
if ($navdraweropen) {
    $extraclasses[] = 'drawer-open-left';
}

if ($draweropenright && $hasblocks) {
    $extraclasses[] = 'drawer-open-right';
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'hasdrawertoggle' => true,
    'navdraweropen' => $navdraweropen,
    'draweropenright' => $draweropenright,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'canviewadmininfos' => false
];

$themesettings = new \theme_hurix\util\theme_settings();

$templatecontext = array_merge($templatecontext, $themesettings->footer_items());

if (is_siteadmin() && $PAGE->pagetype == 'my-index') {
    $adminifos = new \theme_hurix\util\admininfos();

    $templatecontext['totalusage'] = $adminifos->get_totaldiskusage();
    $templatecontext['totalactiveusers'] = $adminifos->get_totalactiveusers();
    $templatecontext['totalsuspendedusers'] = $adminifos->get_suspendedusers();
    $templatecontext['totalcourses'] = $adminifos->get_totalcourses();
    $templatecontext['onlineusers'] = $adminifos->get_totalonlineusers();

    $templatecontext['canviewadmininfos'] = true;
}else if(is_student()){
   $quizesCount=0;
    $courses=enrol_get_my_courses();
      $coursestatus= course_classify_courses_for_timeline($courses);
      //echo"<pre>";print_r($coursestatus);die;
      $badges=badges_get_user_badges($USER->id);
       $notifications = \message_popup\api::get_popup_notifications();
       foreach($courses as $k=>$v){
           $courseModules=get_coursemodules_in_course('quiz',$v->id);
           $quizesCount=$quizesCount+count($courseModules);
           unset($courseModules); 
       }
 
    $templatecontext['enrolledcourses'] = count($courses);
    $templatecontext['progresscourses'] = count($coursestatus['inprogress']);
    $templatecontext['completedcourses'] = count($coursestatus['past']);
    $templatecontext['upcomingcourses'] = count($coursestatus['feature']);
    $templatecontext['quizesdo'] = $quizesCount;
    $templatecontext['earnedbadges'] = count($badges);
    $templatecontext['certificates'] = 0;
    $templatecontext['notifications'] = count($notifications);
    
    $templatecontext['canviewstudentinfo'] = true;
}else if(is_teacher()){
    $students=0;
    $courses=enrol_get_my_courses();
    $notifications = \message_popup\api::get_popup_notifications();
       foreach($courses as $k=>$v){
           $courseModules=get_coursemodules_in_course('quiz',$v->id);
           $quizesCount=$quizesCount+count($courseModules);
           unset($courseModules); 
       }
       
       foreach($courses as $k=>$v){
        $context = context_course::instance($v->id);  
        $students=$students+count_enrolled_users($context);
        unset($context);
       }
         
  
     
   $templatecontext['enrolledcourses'] = count($courses); 
   $templatecontext['mysatudent'] = $students;
   $templatecontext['assigements'] = 5;
   $templatecontext['notifications'] = count($notifications);
   $templatecontext['canviewteacherinfo'] = true;   
}


// Improve boost navigation.
theme_hurix_extend_flat_navigation($PAGE->flatnav);

$templatecontext['flatnavigation'] = $PAGE->flatnav;

echo $OUTPUT->render_from_template('theme_hurix/mydashboard', $templatecontext);
