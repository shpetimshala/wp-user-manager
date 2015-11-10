<?php
/**
 * WPUM Template: Upload Field Template.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

$classes            = array( 'input-upload' );
$allowed_mime_types = array_keys( ! empty( $field['allowed_mime_types'] ) ? $field['allowed_mime_types'] : get_allowed_mime_types() );
$field_name         = isset( $field['name'] ) ? $field['name'] : $key;
$field_name         .= ! empty( $field['multiple'] ) ? '[]' : '';

// Store current field files.
$field_files = $field['value'];

?>

<?php if( ! is_page( wpum_get_core_page_id( 'register' ) ) ) : ?>

	<div class="wpum-uploaded-files">

		<?php

		if ( ! empty( $field['value'] ) && ! is_wp_error( $field['value'] ) ) :

			// Check if we have multiple files.
			if( wpum_is_multi_array( $field_files ) ) {

				foreach ( $field_files as $key => $file ) {

					get_wpum_template( 'form-fields/uploaded-file-html.php',
					    array(
					      'key'        => $key,
					      'name'       => 'current_' . $field_name,
					      'value'      => $file['url'],
					      'field'      => $field,
					      'field_name' => $field_name
					    )
					);

				}

			// We have single file.
			} else {

				get_wpum_template( 'form-fields/uploaded-file-html.php',
						array(
							'key'        => $key,
							'name'       => 'current_' . $field_name,
							'value'      => $field_files,
							'field'      => $field,
							'field_name' => $field_name
						)
				);

			}

		endif;

		?>

	</div>

<?php endif; ?>

<input type="file" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" <?php if ( ! empty( $field['multiple'] ) ) echo 'multiple'; ?> name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?><?php if ( ! empty( $field['multiple'] ) ) echo '[]'; ?>" id="<?php echo esc_attr( $key ); ?>" />

<small class="description">
	<?php if ( ! empty( $field['description'] ) ) : ?>
		<?php echo $field['description']; ?>
	<?php else : ?>
		<?php printf( __( 'Maximum file size: %s.', 'wpum' ), wpum_max_upload_size( $field_name ) ); ?>
	<?php endif; ?>
</small>
