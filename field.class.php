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
 * Main Course profile field.
 *
 * @package    profilefield_maincourse
 * @copyright  2014 Paul Vaughan, based on work (c) 2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class profile_field_maincourse
 *
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_maincourse extends profile_field_base {

    /** @var array $options */
    public $options;

    /** @var int $datakey */
    public $datakey;

    /**
     * Constructor method.
     *
     * Pulls out the options for the menu from the database and sets the the corresponding key for the data if it exists.
     *
     * @param int $fieldid
     * @param int $userid
     */
    public function profile_field_maincourse($fieldid = 0, $userid = 0) {
        global $DB;

        // First call parent constructor.
        $this->profile_field_base($fieldid, $userid);

        // Database query to pull this user's enrolments. Note that the context level is hard-coded.
        $courses = $DB->get_records_sql("SELECT DISTINCT c.id AS id, c.fullname, c.shortname, c.idnumber, c.visible
            FROM {role_assignments} ra, {user} u,
                {course} c, {context} cxt, {role} r
            WHERE ra.userid = u.id
            AND ra.contextid = cxt.id
            AND cxt.contextlevel = 50
            AND cxt.instanceid = c.id
            AND ra.roleid = r.id
            AND u.id = ?
            ORDER BY fullname ASC;", array($userid)
        );

        // Create a nice-looking array of the returned data.
        $options = array();
        foreach ($courses as $course) {
            if ($course->visible = 1) {
                $options[$course->id] = $course->fullname.' ('.$course->shortname.') [#'.$course->id.']';
            } else {
                $options[$course->id] = $course->fullname.' ('.$course->shortname.') [#'.$course->id.'] (hidden)';
            }
        }

        // Remove any courses we don't want appearing in this list.
        // Notes: Hard-coded for now; specific to live Moodle only.
        $dontshow = array(
            685,    // Computer Services
            490,    // E & D Zone
            78,     // Helpzone
            277,    // Learning Resources (LTRS)
            870,    // SDC e-Library
            660     // Student Union
        );
        foreach ($options as $key => $option) {
            foreach ($dontshow as $ds) {
                if ($key == $ds) {
                    unset($options[$key]);
                }
            }
        }

        $this->options = array();
        if (!empty($this->field->required)) {
            $this->options[''] = get_string('choose').'...';
        }
        foreach ($options as $key => $option) {
            $this->options[$key] = format_string($option); // Multilang formatting.
        }

        // Set the data key.
        if ($this->data !== null) {
            $this->datakey = (int)array_search($this->data, $this->options);
        }
    }

    /**
     * Create the code snippet for this field instance
     * Overwrites the base class method
     * @param moodleform $mform Moodle form instance
     */
    public function edit_field_add($mform) {
        $mform->addElement('select', $this->inputname, format_string($this->field->name), $this->options);
    }

    /**
     * Set the default value for this field instance
     * Overwrites the base class method.
     * @param moodleform $mform Moodle form instance
     */
    public function edit_field_set_default($mform) {
        if (false !== array_search($this->field->defaultdata, $this->options)) {
            $defaultkey = (int)array_search($this->field->defaultdata, $this->options);
        } else {
            $defaultkey = '';
        }
        $mform->setDefault($this->inputname, $defaultkey);
    }

    /**
     * The data from the form returns the key.
     *
     * This should be converted to the respective option string to be saved in database
     * Overwrites base class accessor method.
     *
     * @param mixed $data The key returned from the select input in the form
     * @param stdClass $datarecord The object that will be used to save the record
     * @return mixed Data or null
     */
    public function edit_save_data_preprocess($data, $datarecord) {
        return isset($this->options[$data]) ? $this->options[$data] : null;
    }

    /**
     * When passing the user object to the form class for the edit profile page
     * we should load the key for the saved data
     *
     * Overwrites the base class method.
     *
     * @param stdClass $user User object.
     */
    public function edit_load_user_data($user) {
        $user->{$this->inputname} = $this->datakey;
    }

    /**
     * HardFreeze the field if locked.
     * @param moodleform $mform instance of the moodleform class
     */
    public function edit_field_set_locked($mform) {
        if (!$mform->elementExists($this->inputname)) {
            return;
        }
        if ($this->is_locked() and !has_capability('moodle/user:update', context_system::instance())) {
            $mform->hardFreeze($this->inputname);
            $mform->setConstant($this->inputname, $this->datakey);
        }
    }
    /**
     * Convert external data (csv file) from value to key for processing later by edit_save_data_preprocess
     *
     * @param string $value one of the values in menu options.
     * @return int options key for the menu
     */
    public function convert_external_data($value) {
        $retval = array_search($value, $this->options);

        // If value is not found in options then return null, so that it can be handled
        // later by edit_save_data_preprocess.
        if ($retval === false) {
            $retval = null;
        }
        return $retval;
    }
}


