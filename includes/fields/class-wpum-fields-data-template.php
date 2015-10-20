<?php
/**
 * This class is responsible for loading the profile, groups and data and displaying it.
 *
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Fields_Data_Template Class.
 * The profile fields loop class.
 *
 * @since 1.2.0
 */
class WPUM_Fields_Data_Template {

    /**
     * The loop iterator.
     *
     * @since 1.2.0
     * @var int
     */
    public $current_group = -1;

    /**
     * The number of groups returned by the query.
     *
     * @since 1.2.0
     * @var int
     */
    public $group_count;

    /**
     * List of groups found by the query.
     *
     * @since 1.2.0
     * @var array
     */
    public $groups;

    /**
     * The current group object being iterated on.
     *
     * @since 1.2.0
     * @var object
     */
    public $group;

    /**
     * The current field.
     *
     * @since 1.2.0
     * @var int
     */
    public $current_field = -1;

    /**
     * The field count.
     *
     * @since 1.2.0
     * @var int
     */
    public $field_count;

    /**
     * Whether the field has data.
     *
     * @since 1.2.0
     * @var bool
     */
    public $field_has_data;

    /**
     * The field.
     *
     * @since 1.2.0
     * @var int
     */
    public $field;

    /**
     * Flag to check whether the loop is currently being iterated.
     *
     * @since 1.2.0
     * @var bool
     */
    public $in_the_loop;

    /**
     * The user id.
     *
     * @since 1.2.0
     * @var int
     */
    public $user_id;

    /**
     * Let's get things going.
     *
     * @since 1.2.0
     * @param array $args arguments.
     */
    public function __construct( $args = '' ) {

        $defaults = array(
            'user_id'           => false,
            'field_group_id'    => false,
            'number'            => false,
            'hide_empty_groups' => true,
            'hide_empty_fields' => false,
            'exclude_groups'    => false,
            'exclude_fields'    => false,
            'orderby'           => 'id',
            'order'             => 'ASC',
            'array'             => true
        );

        // Parse incoming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );

        $this->groups      = wpum_get_field_groups( $args );
        $this->group_count = count( $this->groups );
        $this->user_id     = $args['user_id'];

    }

    /**
     * Whether there are groups available.
     *
     * @since 1.2.0
     * @access public
     * @return boolean
     */
    public function has_groups() {

        if( ! empty( $this->group_count ) ) {
            return true;
        }

        return false;

    }

    /**
     * Get next group within the loop.
     *
     * @since 1.2.0
     * @access public
     * @return array
     */
    public function next_group() {

        $this->current_group++;

        $this->group = $this->groups[ $this->current_group ];
        $this->field_count = 0;

        if( ! empty( $this->group->fields ) ) {
            $this->group->fields = apply_filters( 'wpum_group_fields', $this->group->fields, $this->group->id );
            $this->field_count = count( $this->group->fields );
        }

        return $this->group;

    }

    /**
     * Rewind groups.
     *
     * @since 1.2.0
     * @access public
     * @return void
     */
    public function rewind_groups() {

        $this->current_group = -1;
        if( $this->group_count > 0 ) {
            $this->group = $this->groups[0];
        }

    }

    /**
     * Check whether we've reached the end of the loop or keep looping.
     *
     * @since 1.2.0
     * @access public
     * @return bool
     */
    public function profile_groups() {

        if( $this->current_group + 1 < $this->group_count ) {
            return true;
        } elseif ( $this->current_group + 1 == $this->group_count ) {
            do_action( 'wpum_field_groups_loop_end' );
            $this->rewind_groups();
        }

        $this->in_the_loop = false;
        return false;

    }

    /**
     * Setup global variable for current group within the loop.
     *
     * @global $wpum_fields_group
     * @since 1.2.0
     * @access public
     * @return [type] [description]
     */
    public function the_profile_group() {

        global $wpum_fields_group;

        $this->in_the_loop = true;
        $wpum_fields_group = $this->next_group();

        if( 0 === $this->current_group ) {
            do_action( 'wpum_field_groups_loop_start' );
        }

    }

    public function has_fields() {

        $has_data = false;

        for ( $i = 0, $count = count( $this->group['fields'] ); $i < $count; ++$i ) {
            $field = &$this->group['fields'][ $i ];

            if ( ! empty( $field->value ) ) {
                $has_data = true;
            }
		}

        return $has_data;

    }

    public function next_field() {

        $this->current_field++;

        $this->field = $this->group['fields'][ $this->current_field ];

        return $this->field;

    }

    public function rewind_fields() {

        $this->current_field = -1;
        if( $this->field_count > 0 ) {
            $this->field = $this->group['fields'][0];
        }

    }

    public function profile_fields() {

        if ( $this->current_field + 1 < $this->field_count ) {
            return true;
        } elseif ( $this->current_field + 1 == $this->field_count ) {
            $this->rewind_fields();
        }

        return false;

    }

    public function the_profile_field() {

        global $wpum_field;

        $wpum_field = $this->next_field();

        if ( ! empty( $wpum_field->data ) && ( ! empty( $wpum_field->data->value ) || ( '0' === $wpum_field->data->value ) ) ) {
            $value = maybe_unserialize( $wpum_field->data->value );
        } else {
            $value = false;
        }

        if ( ! empty( $value ) || ( '0' === $value ) ) {
            $this->field_has_data = true;
        } else {
            $this->field_has_data = false;
		}

	}

}
