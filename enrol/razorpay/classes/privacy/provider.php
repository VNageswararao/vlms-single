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
 * Privacy Subsystem implementation for enrol_razorpay.
 *
 * @package    enrol_razorpay
 * @category   privacy
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_razorpay\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation for enrol_razorpay.
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // Transactions store user data.
        \core_privacy\local\metadata\provider,

        // The razorpay enrolment plugin contains user's transactions.
        \core_privacy\local\request\plugin\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_external_location_link(
            'razorpay.com',
            [
                'os0'        => 'privacy:metadata:enrol_razorpay:razorpay_com:os0',
                'custom'     => 'privacy:metadata:enrol_razorpay:razorpay_com:custom',
                'first_name' => 'privacy:metadata:enrol_razorpay:razorpay_com:first_name',
                'last_name'  => 'privacy:metadata:enrol_razorpay:razorpay_com:last_name',
                'address'    => 'privacy:metadata:enrol_razorpay:razorpay_com:address',
                'city'       => 'privacy:metadata:enrol_razorpay:razorpay_com:city',
                'email'      => 'privacy:metadata:enrol_razorpay:razorpay_com:email',
                'country'    => 'privacy:metadata:enrol_razorpay:razorpay_com:country',
            ],
            'privacy:metadata:enrol_razorpay:razorpay_com'
        );

        // The enrol_razorpay has a DB table that contains user data.
        $collection->add_database_table(
                'enrol_razorpay',
                [
                    'business'            => 'privacy:metadata:enrol_razorpay:enrol_razorpay:business',
                    'receiver_email'      => 'privacy:metadata:enrol_razorpay:enrol_razorpay:receiver_email',
                    'receiver_id'         => 'privacy:metadata:enrol_razorpay:enrol_razorpay:receiver_id',
                    'item_name'           => 'privacy:metadata:enrol_razorpay:enrol_razorpay:item_name',
                    'courseid'            => 'privacy:metadata:enrol_razorpay:enrol_razorpay:courseid',
                    'userid'              => 'privacy:metadata:enrol_razorpay:enrol_razorpay:userid',
                    'instanceid'          => 'privacy:metadata:enrol_razorpay:enrol_razorpay:instanceid',
                    'memo'                => 'privacy:metadata:enrol_razorpay:enrol_razorpay:memo',
                    'tax'                 => 'privacy:metadata:enrol_razorpay:enrol_razorpay:tax',
                    'option_selection1_x' => 'privacy:metadata:enrol_razorpay:enrol_razorpay:option_selection1_x',
                    'payment_status'      => 'privacy:metadata:enrol_razorpay:enrol_razorpay:payment_status',
                    'pending_reason'      => 'privacy:metadata:enrol_razorpay:enrol_razorpay:pending_reason',
                    'reason_code'         => 'privacy:metadata:enrol_razorpay:enrol_razorpay:reason_code',
                    'txn_id'              => 'privacy:metadata:enrol_razorpay:enrol_razorpay:txn_id',
                    'parent_txn_id'       => 'privacy:metadata:enrol_razorpay:enrol_razorpay:parent_txn_id',
                    'payment_type'        => 'privacy:metadata:enrol_razorpay:enrol_razorpay:payment_type',
                    'timeupdated'         => 'privacy:metadata:enrol_razorpay:enrol_razorpay:timeupdated'
                ],
                'privacy:metadata:enrol_razorpay:enrol_razorpay'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new contextlist();

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by razorpay,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT ctx.id
                  FROM {enrol_razorpay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {context} ctx ON e.courseid = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {user} u ON u.id = ep.userid OR LOWER(u.email) = ep.receiver_email OR LOWER(u.email) = ep.business
                 WHERE u.id = :userid";
        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'userid'        => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by razorpay,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT u.id
                  FROM {enrol_razorpay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {user} u ON ep.userid = u.id OR LOWER(u.email) = ep.receiver_email OR LOWER(u.email) = ep.business
                 WHERE e.courseid = :courseid";
        $params = ['courseid' => $context->instanceid];

        $userlist->add_from_sql('id', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        // Values of ep.receiver_email and ep.business are already normalised to lowercase characters by razorpay,
        // therefore there is no need to use LOWER() on them in the following query.
        $sql = "SELECT ep.*
                  FROM {enrol_razorpay} ep
                  JOIN {enrol} e ON ep.instanceid = e.id
                  JOIN {context} ctx ON e.courseid = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {user} u ON u.id = ep.userid OR LOWER(u.email) = ep.receiver_email OR LOWER(u.email) = ep.business
                 WHERE ctx.id {$contextsql} AND u.id = :userid
              ORDER BY e.courseid";

        $params = [
            'contextcourse' => CONTEXT_COURSE,
            'userid'        => $user->id,
            'emailuserid'   => $user->id,
        ];
        $params += $contextparams;

        // Reference to the course seen in the last iteration of the loop. By comparing this with the current record, and
        // because we know the results are ordered, we know when we've moved to the razorpay transactions for a new course
        // and therefore when we can export the complete data for the last course.
        $lastcourseid = null;

        $strtransactions = get_string('transactions', 'enrol_razorpay');
        $transactions = [];
        $razorpayrecords = $DB->get_recordset_sql($sql, $params);
        foreach ($razorpayrecords as $razorpayrecord) {
            if ($lastcourseid != $razorpayrecord->courseid) {
                if (!empty($transactions)) {
                    $coursecontext = \context_course::instance($razorpayrecord->courseid);
                    writer::with_context($coursecontext)->export_data(
                            [$strtransactions],
                            (object) ['transactions' => $transactions]
                    );
                }
                $transactions = [];
            }

            $transaction = (object) [
                'receiver_id'         => $razorpayrecord->receiver_id,
                'item_name'           => $razorpayrecord->item_name,
                'userid'              => $razorpayrecord->userid,
                'memo'                => $razorpayrecord->memo,
                'tax'                 => $razorpayrecord->tax,
                'option_name1'        => $razorpayrecord->option_name1,
                'option_selection1_x' => $razorpayrecord->option_selection1_x,
                'option_name2'        => $razorpayrecord->option_name2,
                'option_selection2_x' => $razorpayrecord->option_selection2_x,
                'payment_status'      => $razorpayrecord->payment_status,
                'pending_reason'      => $razorpayrecord->pending_reason,
                'reason_code'         => $razorpayrecord->reason_code,
                'txn_id'              => $razorpayrecord->txn_id,
                'parent_txn_id'       => $razorpayrecord->parent_txn_id,
                'payment_type'        => $razorpayrecord->payment_type,
                'timeupdated'         => \core_privacy\local\request\transform::datetime($razorpayrecord->timeupdated),
            ];
            if ($razorpayrecord->userid == $user->id) {
                $transaction->userid = $razorpayrecord->userid;
            }
            if ($razorpayrecord->business == \core_text::strtolower($user->email)) {
                $transaction->business = $razorpayrecord->business;
            }
            if ($razorpayrecord->receiver_email == \core_text::strtolower($user->email)) {
                $transaction->receiver_email = $razorpayrecord->receiver_email;
            }

            $transactions[] = $razorpayrecord;

            $lastcourseid = $razorpayrecord->courseid;
        }
        $razorpayrecords->close();

        // The data for the last activity won't have been written yet, so make sure to write it now!
        if (!empty($transactions)) {
            $coursecontext = \context_course::instance($razorpayrecord->courseid);
            writer::with_context($coursecontext)->export_data(
                    [$strtransactions],
                    (object) ['transactions' => $transactions]
            );
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_course) {
            return;
        }

        $DB->delete_records('enrol_razorpay', array('courseid' => $context->instanceid));
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        $contexts = $contextlist->get_contexts();
        $courseids = [];
        foreach ($contexts as $context) {
            if ($context instanceof \context_course) {
                $courseids[] = $context->instanceid;
            }
        }

        list($insql, $inparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);

        $select = "userid = :userid AND courseid $insql";
        $params = $inparams + ['userid' => $user->id];
        $DB->delete_records_select('enrol_razorpay', $select, $params);

        // We do not want to delete the payment record when the user is just the receiver of payment.
        // In that case, we just delete the receiver's info from the transaction record.

        $select = "business = :business AND courseid $insql";
        $params = $inparams + ['business' => \core_text::strtolower($user->email)];
        $DB->set_field_select('enrol_razorpay', 'business', '', $select, $params);

        $select = "receiver_email = :receiver_email AND courseid $insql";
        $params = $inparams + ['receiver_email' => \core_text::strtolower($user->email)];
        $DB->set_field_select('enrol_razorpay', 'receiver_email', '', $select, $params);
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $userids = $userlist->get_userids();

        list($usersql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        $params = ['courseid' => $context->instanceid] + $userparams;

        $select = "courseid = :courseid AND userid $usersql";
        $DB->delete_records_select('enrol_razorpay', $select, $params);

        // We do not want to delete the payment record when the user is just the receiver of payment.
        // In that case, we just delete the receiver's info from the transaction record.

        $select = "courseid = :courseid AND business IN (SELECT LOWER(email) FROM {user} WHERE id $usersql)";
        $DB->set_field_select('enrol_razorpay', 'business', '', $select, $params);

        $select = "courseid = :courseid AND receiver_email IN (SELECT LOWER(email) FROM {user} WHERE id $usersql)";
        $DB->set_field_select('enrol_razorpay', 'receiver_email', '', $select, $params);
    }
}
