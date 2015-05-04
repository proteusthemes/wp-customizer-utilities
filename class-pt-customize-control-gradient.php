<?php

/**
 * Gradient Control Class
 *
 * @package
 * @subpackage Customize
 */
class WP_Customize_Gradient_Control extends WP_Customize_Control {

	public $type = 'gradient-bg';

	// Enqueue scripts/styles for the color picker.
	public function enqueue() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	// Render the gradient control
	public function render_content() {
		$values = $this->value();

		$default_values = $this->setting->default;

	?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>

		<!-- First color picker -->
		<input type="text" id="first-color-<?php echo esc_attr( $this->id ); ?>" class="gradient-color-picker" value="<?php echo esc_attr( $values['start_color'] ); ?>" data-default-color="<?php echo esc_attr( $default_values['start_color'] ); ?>" />

		<!-- Wrap the second color picker with a span so we can hide it -->
		<span class="hide-non-gradient-<?php echo esc_attr( $this->id ); ?>">
			<!-- Second color picker (optional) -->
			<input type="text" id="second-color-<?php echo esc_attr( $this->id ); ?>" class="gradient-color-picker" value="<?php echo esc_attr( $values['stop_color'] ); ?>" data-default-color="<?php echo esc_attr( $default_values['stop_color'] ); ?>" />
		</span>

		<p class="hide-non-gradient-<?php echo esc_attr( $this->id ); ?>">
			<label>
				<?php _e( 'Gradient angle: ', 'cargopress-pt' ) ?>
				<div class="hide-non-gradient-<?php echo esc_attr( $this->id ); ?>" style="text-align: center;">
					<!-- Range control for gradient angle -->
					<input type="range" id="range-<?php echo esc_attr( $this->id ); ?>"  value="<?php echo esc_attr( $values['gradient_angle'] ); ?>" min="0" max="180" step="15" />
				</div>
			</label>
		</p>
		</span>
		<p>
			<label>
				<?php _e( 'Use gradient: ', 'cargopress-pt' ) ?>
				<!-- Checkbox for enable/disable gradient or single color control -->
				<input type="checkbox" id="gradient-checkbox-<?php echo esc_attr( $this->id ); ?>" <?php checked( $values['is_gradient'] ); ?> />
			</label>
		</p>

		<script>
			jQuery( function( $ ) {
				'use strict';

				/************ On Load Events ************/

				// Get the starting values from PHP
				var values = <?php echo wp_json_encode( $values ); ?>;

				// Display or hide the gradient controls (second color and the range control)
				$( '.hide-non-gradient-<?php echo esc_attr( $this->id ); ?>' ).toggle( values['is_gradient'] );

				/********* END: On Load Events **********/

				// Saving settings for customizer via JS wp.customize
				var setSettings = function( val ) {
					wp.customize( '<?php echo esc_js( $this->id ); ?>', function( obj ) {
						// Reset the setting value, so that the change is triggered
						obj.set( '' );
						// Set the right value
						obj.set( val );
						// Refresh the preview to apply the gradient control changes
						obj.previewer.refresh();
					} );
				};

				// Convert input fields to color pickers
				$('.gradient-color-picker').wpColorPicker( {
					// A callback to fire whenever the color changes to a valid color
					change: function( event, ui ){
						switch( event.target.id ) {
							case 'first-color-<?php echo esc_attr( $this->id ); ?>':
								values['start_color'] = ui.color.toString();
								break;
							case 'second-color-<?php echo esc_attr( $this->id ); ?>':
								values['stop_color'] = ui.color.toString();
								break;
						}
						// Set customizer settings with new colors
						setSettings( values );
					},
				} );

				// Display/hide and set gradient controls on checkbox click
				$( '#gradient-checkbox-<?php echo esc_attr( $this->id ); ?>' ).click( function( event ) {
					values['is_gradient'] = this.checked;
					$( '.hide-non-gradient-<?php echo esc_attr( $this->id ); ?>' ).toggle( values['is_gradient'] );
					setSettings( values );
				});

				// Set updated gradient angle value.
				$( '#range-<?php echo esc_attr( $this->id ); ?>' ).change( function( event ) {
					values['gradient_angle'] = parseInt( event.currentTarget.value );
					setSettings( values );
				});

			} );
		</script>
	<?php
	}
}