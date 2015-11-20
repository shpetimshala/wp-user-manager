<?php
/**
 * WPUM Template: Directory search form.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.0
 */
?>
<div class="wpum-directory-search-form-wrapper">

  <form action="#" method="post" id="wpum-directory-search-form-<?php echo $directory_args['directory_id']; ?>" class="wpum-directory-search-form" name="wpum-directory-search-form">

    <div class="form-fields">
      <?php
        $search_input = array(
          'name'        => 'search_user',
          'value'       => '',
          'placeholder' => esc_html__( 'Search for users' ),
        );
        echo WPUM()->html->text( $search_input );
      ?>
    </div>

    <div class="form-submit">
      <input type="submit" name="wpum_submit_user_search" id="wpum-submit-user-search" class="button wpum-button" value="<?php esc_html_e( 'Search' ); ?>">
    </div>

    <div class="wpum-clearfix"></div>

  </form>

</div>
