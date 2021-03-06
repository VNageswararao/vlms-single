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
 * Integer field.
 *
 * This is almost 100% based on the 'integer' field from block_stash.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/group.php');

/**
 * Integer field class.
 *
 * Returns an integer, or null for disabled.
 *
 * Note, this is not namespaced to allow for compability with old-style constructor methods.
 * Those are required in older versions of Moodle: < 3.0.1.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mootivated_form_integer extends MoodleQuickForm_group {

    /**
     * Constructor.
     *
     * @param string $elementname The name.
     * @param string $elementlabel The label.
     * @param array $attributes Attributes.
     */
    public function __construct($elementname = null, $elementlabel = null, $attributes = array()) {
        $attributes = (array) $attributes;
        if (!isset($attributes['min'])) {
            $attributes['min'] = 1;
        }
        if (!isset($attributes['initial'])) {
            $attributes['initial'] = 30;
        }

        parent::__construct($elementname, $elementlabel);
        $this->setAttributes(array_merge((array) $this->_attributes, $attributes));

        // The type of this element must not be changed or it creates a series of unpredictable
        // in Boost (3.2 onwards) as the Mustache templates would not be picked adequately.
        // $this->_type = 'integer';
    }

    /**
     * Old-style constructor.
     * @see self::__construct()
     */
    public function local_mootivated_form_integer($elementname = null, $elementlabel = null, $attributes = array()) {
        self::__construct($elementname, $elementlabel, $attributes);
    }

    /**
     * Override of standard quickforms method to create this element.
     *
     * @return void
     */
    function _createElements() {
        $attributes = (array) $this->getAttributes();

        if (!isset($attributes['class'])) {
            $attributes['class'] = '';
        }
        $attributes['class'] .= ' form-control';

        if (method_exists($this, 'createFormElement')) {
            $element = $this->createFormElement('text', 'int', get_string('coins', 'local_mootivated'), $attributes);
        } else {
            $element = @MoodleQuickForm::createElement('text', 'int', get_string('coins', 'local_mootivated'), $attributes);
        }
        $element->setType('number');
        $element->setHiddenLabel(true);

        $this->_elements = [];
        $this->_elements[] = $element;
        if (method_exists($this, 'createFormElement')) {
            $this->_elements[] = $this->createFormElement('checkbox', 'dis', null,
                get_string('disabled', 'local_mootivated'));
        } else {
            $this->_elements[] = @MoodleQuickForm::createElement('checkbox', 'dis', null,
                get_string('disabled', 'local_mootivated'));
        }
    }

    /**
     * Returns a 'safe' element's value.
     *
     * @param  array   array of submitted values to search
     * @param  bool    whether to return the value as associative array
     * @return mixed
     */
    function exportValue(&$submitValues, $assoc = false) {
        $name = $this->getName();
        $disabled = !empty($submitValues[$name]['dis']);
        if ($disabled) {
            return [$name => null];
        }
        return [$name => $submitValues[$name]['int']];
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller) {
        if (method_exists($this, 'setMoodleForm')) {
            $this->setMoodleForm($caller);
        }
        switch ($event) {
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    if ($caller->isSubmitted()) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }

                $minvalue = $this->_attributes['min'];
                $initialvalue = $this->_attributes['initial'];
                if (!is_array($value)) {
                    $displayval = $value === null ? $initialvalue : (int) $value;
                    $value = ['int' => max($minvalue, $displayval), 'dis' => $value === null];
                } else {
                    $value = [
                        'int' => isset($value['int']) ? $value['int'] : $initialvalue,
                        'dis' => !empty($value['dis'])
                    ];
                }

                $this->setValue($value);
                break;

            case 'createElement':
                $caller->disabledIf($arg[0], $arg[0] . '[dis]', 'checked');
                $caller->setType($arg[0] . '[int]', PARAM_INT);
                return parent::onQuickFormEvent($event, $arg, $caller);
                break;

            default:
                return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }
}
