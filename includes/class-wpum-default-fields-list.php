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

        $data = WPUM_Default_Fields_Editor::default_user_fields_list();

        /* Modify the order of the data based on what's already saved into the database */
        $saved_order = get_option( 'wpum_default_fields' );
        
        if( $saved_order ) {
            foreach ($saved_order as $field) {
                $data[ $field['meta'] ]['order'] = $field['order'];
                $data[ $field['meta'] ]['required'] = $field['required'];
                $data[ $field['meta'] ]['show_on_signup'] = $field['show_on_signup'];
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

        if($item['meta'] !== 'nickname' && $item['meta'] !== 'password' && $item['meta'] !== 'user_email' ) {
            $edit_url = add_query_arg( array('field' => $item['meta'], 'required' => $item['required'], 'wpum_action' => 'edit_default_field'), admin_url( 'users.php?page=wpum-edit-default-field' ) );
            echo '<a href="'.$edit_url.'" class="button wpum-trigger-modal" data-field="'.$item['meta'].'">'.__('Edit Field').'</a> ';
        }
        
        echo '<a href="#" class="button move-field"><span class="dashicons dashicons-sort"></span></a>';

        echo $this->add_modal_window( $item );

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
 
        echo '<tr' . $row_class . $row_id . ' data-order="'.$item['order'].'" data-meta="'.$item['meta'].'" data-required="'.$item['required'].'" data-show_on_signup="'.$item['show_on_signup'].'">';
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    /**
     * Injects modal window into table for field modification.
     *
     * @access public
     * @param object $item The current item
     */
    public function add_modal_window( $item ) {
        ?>

        <div class="wpum-window-hide wpum-fields-window-editor" id="window-<?php echo $item['meta']; ?>">
            <form action="#" method="post" id="wpum-field-update-<?php echo $item['meta']; ?>" class="wpum-field-update" name="wpum-field-update">
                <div class="media-modal wp-core-ui">

                    <a class="media-modal-close" href="#" title="Close">
                        <span class="media-modal-icon"></span>
                    </a>

                    <div class="media-modal-content">
                        <div class="media-frame wp-core-ui">

                            <div class="media-frame-title">
                                <h1><?php printf( __('Edit "%s" field'), $item['title'] ); ?></h1>
                            </div><!-- .media-frame-title (end) -->

                            <div id="optionsframework" class="media-frame-content">
                                <div class="attachments-browser">

                                    <div class="section_clearfix section">
                                        <div class="section-description">
                                            <strong><?php _e('Set field as required');?></strong>
                                            <span><?php _e('Enable this option to set this field as required.');?></span>
                                        </div>
                                        <div class="section-form-element ">
                                            <select class="" id="<?php echo $item['meta']; ?>_field_required" name="<?php echo $item['meta']; ?>_field_required">
                                                <option value="1" <?php selected( $item['required'], true ); ?>><?php _e('Yes');?></option>
                                                <option value="" <?php selected( $item['required'], false ); ?>><?php _e('No');?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="section_clearfix section">
                                        <div class="section-description">
                                            <strong><?php _e('Show in registration form');?></strong>
                                            <span><?php _e('Enable this option to display this field into the registration form.');?></span>
                                        </div>
                                        <div class="section-form-element ">
                                        <?php echo $item['show_on_signup']; ?>
                                        <select class="" id="<?php echo $item['meta']; ?>_field_display" name="<?php echo $item['meta']; ?>_field_display">
                                                <option value="1" <?php selected( $item['show_on_signup'], true ); ?>><?php _e('Yes');?></option>
                                                <option value="" <?php selected( $item['show_on_signup'], false ); ?>><?php _e('No');?></option>
                                            </select>
                                        </div>
                                    </div>

                                </div><!-- .attachments-browser (end) -->
                            </div><!-- .media-frame-content (end) -->

                            <div class="media-frame-toolbar">
                                <div class="media-toolbar">
                                    <div class="media-toolbar-secondary">

                                    </div>
                                    <div class="media-toolbar-primary">
                                        <input type="hidden" name="wpum_field_submit" value="<?php echo $item['meta']; ?>" />
                                        <?php wp_nonce_field( $item['meta'], $item['meta'] ); ?>
                                        <button href="#" data-insert="button" class="button media-button button-primary button-large"><?php _e('Update field'); ?></button>
                                    </div>
                                </div><!-- .media-toolbar (end) -->
                            </div><!-- .media-frame-toolbar (end) -->

                        </div><!-- .media-frame (end) -->
                    </div><!-- .media-modal-content (end) -->

                </div><!-- .media-modal (end) -->
            </form>
            <div class="media-modal-backdrop"></div>
        </div>

        <?php
    }

}