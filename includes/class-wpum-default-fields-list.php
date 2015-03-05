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
            'meta'     => __('Meta name'),
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

        $data = array();

        $data[] = array(
            'order'    => 1,
            'title'    => 'Test 1',
            'type'     => 'Text',
            'meta'     => 'test',
            'required' => 'yes',
        );

        $data[] = array(
            'order'    => 2,
            'title'    => 'Test 2',
            'type'     => 'Text',
            'meta'     => 'test_2',
            'required' => 'no',
        );

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
                return $item['type'];
            break;
            case 'meta':
                return $item['meta'];
            break;
            case 'required':
                return $item['required'];
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

        return null;

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

}