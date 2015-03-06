<?php
/**
 * Default Fields List
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Default_Fields_List
 * Create a table with the list of default fields.
 * 
 * @since 1.0.0
 */
class WPUM_Default_Fields_List extends WP_List_Table {

    /**
     * Prepare the items for the table to process
     *
     * @since 1.0.0
     * @return Void
     */
    public function prepare_items() {

        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;

    }

    /**
     * Override the parent columns method. Defines the columns to use in the listing table
     *
     * @since 1.0.0
     * @return Array
     */
    public function get_columns() {
        
        $columns = array(
            'order'    => '<span class="dashicons dashicons-sort"></span>',
            'title'    => __('Field Title'),
            'type'     => __('Field Type'),
            'required' => __('Required'),
            'actions'  => __('Actions'),
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     * 
     * @since 1.0.0
     * @return Array
     */
    public function get_hidden_columns() {
        return array( );
    }

    /**
     * Define the sortable columns
     * 
     * @since 1.0.0
     * @return Array
     */
    public function get_sortable_columns() {
        return null;
    }

    /**
     * Get the table data
     * 
     * @since 1.0.0
     * @return Array
     */
    private function table_data() {

        $data = array();

        $data['first_name'] = array(
            'order'    => 0,
            'title'    => __('First Name'),
            'type'     => 'text',
            'meta'     => 'first_name',
            'required' => false,
        );

        $data['last_name'] = array(
            'order'    => 1,
            'title'    => __('Last Name'),
            'type'     => 'text',
            'meta'     => 'last_name',
            'required' => false,
        );

        $data['nickname'] = array(
            'order'    => 2,
            'title'    => __('Nickname'),
            'type'     => 'text',
            'meta'     => 'nickname',
            'required' => true,
        );

        $data['display_name'] = array(
            'order'    => 3,
            'title'    => __('Display Name'),
            'type'     => 'select',
            'meta'     => 'display_name',
            'required' => true,
        );

        $data['user_email'] = array(
            'order'    => 4,
            'title'    => __('Email'),
            'type'     => 'email',
            'meta'     => 'user_email',
            'required' => true,
        );

        $data['user_url'] = array(
            'order'    => 5,
            'title'    => __('Website'),
            'type'     => 'text',
            'meta'     => 'user_url',
            'required' => false,
        );

        $data['description'] = array(
            'order'    => 6,
            'title'    => __('Description'),
            'type'     => 'textarea',
            'meta'     => 'description',
            'required' => false,
        );

        $data['password'] = array(
            'order'    => 7,
            'title'    => __('Password'),
            'type'     => 'password',
            'meta'     => 'password',
            'required' => true,
        );

        /* Modify the order of the data based on what's already saved into the database */
        $saved_order = get_option( 'wpum_default_fields' );
        
        if( $saved_order ) {
            foreach ($saved_order as $field) {
                $data[ $field['meta'] ]['order'] = $field['order'];
                $data[ $field['meta'] ]['required'] = $field['required'];
            }
        }

        // Sort all together
        uasort( $data, 'wpum_sort_default_fields_table');

        return $data;

    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name ) {
        
        switch( $column_name ) {
            case 'order':
                return $item['order'];
            break;
            case 'title':
                return $item['title'];
            break;
            case 'type':
                return $this->parse_field_name( $item['type'] );
            break;
            case 'meta':
                return $item['meta'];
            break;
            case 'required':
                return $this->parse_field_required( $item['required'] );
            break;
            case 'actions':
                return $this->table_actions($item);
            break;
            default:
                return null;
        }

    }

    /**
     * Generate the table navigation above or below the table
     *
     * Overwriting this method allows to correctly save the options page
     * because this method adds new nonce fields too.
     *
     * @since 1.0.0
     * @access protected
     * @param string $which
     */
    protected function display_tablenav( $which ) {
        return null;
    }

    /**
     * Displays edit button for the email.
     *
     * @param   array $item - The email item being passed
     * @return  Mixed
     */
    private function table_actions( $item ) {

        $edit_url = add_query_arg( array('field' => $item['meta'], 'required' => $item['required'], 'wpum_action' => 'edit_default_field'), admin_url( 'users.php?page=wpum-edit-default-field' ) );
        echo '<a href="'.$edit_url.'" class="button">'.__('Edit Field').'</a> ';

    }

    /**
     * Get a list of CSS classes for the list table table tag.
     *
     * @access protected
     * @return array List of CSS classes for the table tag.
     */
    protected function get_table_classes() {
        return array( 'widefat', 'fixed', $this->_args['plural'], 'wpum_fields_table_list' );
    }

    /**
     * Displays a translatable string for the field type column.
     *
     * @access public
     * @return string the field type name.
     */
    public function parse_field_name( $type ) {

        $text = __('Text field');

        if( $type == 'email' ) {
            $text = __('Email field');
        } elseif ( $type == 'select' ) {
            $text = __('Select dropdown');
        } elseif ( $type == 'textarea' ) {
            $text = __('Textarea field');
        } elseif ( $type == 'password' ) {
            $text = __('Password field');
        }

        return apply_filters( 'wpum_default_fields_table_field_types', $text );

    }

    /**
     * Displays an icon for the required column
     *
     * @access public
     * @return string whether it's required or not.
     */
    public function parse_field_required( $is_required = false ) {

        $show_icon = '';

        if( $is_required == true ) {
            $show_icon = '<span class="dashicons dashicons-yes"></span>';
        }

        return $show_icon;

    }

    /**
     * Generates content for a single row of the table
     *
     * @access public
     * @param object $item The current item
     */
    public function single_row( $item ) {
        static $row_class = '';
        $row_class = ( $row_class == '' ? ' class="alternate"' : '' );

        // Add id
        $row_id = ' id="'.$item['meta'].'"';
 
        echo '<tr' . $row_class . $row_id . ' data-order="'.$item['order'].'" data-meta="'.$item['meta'].'" data-required="'.$item['required'].'">';
        $this->single_row_columns( $item );
        echo '</tr>';
    }

}