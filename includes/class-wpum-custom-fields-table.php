<?php
/**
 * Custom Fields Editor list table.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Custom_Fields_List
 * Create a table with the list of default fields.
 * 
 * @since 1.0.0
 */
class WPUM_Custom_Fields_List extends WP_List_Table {

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
            'order'    => __('Order'),
            'title'    => __('Field Title'),
            'type'     => __('Field Type'),
            'required' => __('Required'),
            'edit'     => __('Edit'),
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
        return array();
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

        $data = wpum_get_sorted_fields();

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
                return '<a href="#" class="button"><span class="dashicons dashicons-sort"></span></a>';
            break;
            case 'title':
                return $item['label'];
            break;
            case 'type':
                return $this->parse_type( $item['type'] );
            break;
            case 'meta':
                return $item['meta'];
            break;
            case 'required':
                return $this->parse_required( $item['required'] );
            break;
            case 'edit':
                return $this->get_edit_action( $item );
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
     * Get a list of CSS classes for the list table table tag.
     *
     * @access protected
     * @return array List of CSS classes for the table tag.
     */
    protected function get_table_classes() {
        return array( 'widefat', 'fixed', $this->_args['plural'] );
    }

    /**
     * Displays a translatable string for the field type column.
     *
     * @access public
     * @return string the field type name.
     */
    public function parse_type( $type ) {

        $text = __('Text');

        if( $type == 'email' ) {
            $text = __('Email');
        } elseif ( $type == 'select' ) {
            $text = __('Dropdown');
        } elseif ( $type == 'textarea' ) {
            $text = __('Textarea');
        } elseif ( $type == 'password' ) {
            $text = __('Password');
        } elseif ( $type == 'file' ) {
            $text = __('Upload');
        }

        return apply_filters( 'wpum_fields_editor_types', $text );

    }

    /**
     * Displays an icon for the required column
     *
     * @access public
     * @return string whether it's required or not.
     */
    public function parse_required( $is_required = false ) {

        $show_icon = '';

        if( $is_required == true ) {
            $show_icon = '<span class="dashicons dashicons-yes"></span>';
        }

        return $show_icon;

    }

    /**
     * Displays edit button for the email.
     *
     * @param   array $item - The email item being passed
     * @return  Mixed
     */
    private function get_edit_action( $item ) {
        if( wpum_get_field_options( $item['meta'] ) ) :
            $edit_url = esc_url_raw( add_query_arg( array(), admin_url( 'users.php?page=wpum-custom-fields-editor' ) ) );
            echo '<a href="'.$edit_url.'" class="button" data-meta="'. esc_js( $item['meta'] ) .'">'.__('Edit').'</a> ';
            wp_nonce_field( $item['meta'], $item['meta'] );
        endif;
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
 
        echo '<tr' . $row_class . $row_id . ' data-priority="'.$item['priority'].'" data-meta="'.$item['meta'].'" data-required="'.$item['required'].'" data-show_on_signup="'.$item['show_on_signup'].'">';
        $this->single_row_columns( $item );
        echo '</tr>';
    }

}