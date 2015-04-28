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
		$values = explode( ', ', $values );

		$default_values = $this->setting->default;
		$default_values = explode( ', ', $default_values );
		/* values and default_values documentation
		 * $values[0] -> First color (@string hex)
		 * $values[1] -> Second color (@string hex)
		 * $values[2] -> Gradient ( @string true/false)
		 * $values[3] -> Gradient angle (@string number from 0 to 180)
		 */

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
		<input type="text" id="first-color-<?php echo esc_attr( $this->id ); ?>" class="gradient-color-picker" value="<?php echo esc_attr( $values[0] ); ?>" data-default-color="<?php echo esc_attr( $default_values[0] ); ?>" />

		<!-- Wrap the second color picker with a span so we can hide it -->
		<span class="hide-non-gradient-<?php echo esc_attr( $this->id ); ?>">
			<!-- Second color picker (optional) -->
			<input type="text" id="second-color-<?php echo esc_attr( $this->id ); ?>" class="gradient-color-picker" value="<?php echo esc_attr( $values[1] ); ?>" data-default-color="<?php echo esc_attr( $default_values[1] ); ?>" />
		</span>

		<p class="hide-non-gradient-<?php echo esc_attr( $this->id ); ?>">
			<label>
				<?php _e( 'Gradient angle: ', 'cargopress-pt' ) ?>
				<div class="hide-non-gradient-<?php echo esc_attr( $this->id ); ?>" style="text-align: center;">
					<!-- Range control for gradient angle -->
					<input type="range" id="range-<?php echo esc_attr( $this->id ); ?>"  value="<?php echo esc_attr( $values[3] ); ?>" min="0" max="180" step="15" />
				</div>
			</label>
		</p>
		</span>
		<p>
			<label>
				<?php _e( 'Use gradient: ', 'cargopress-pt' ) ?>
				<!-- Checkbox for enable/disable gradient or single color control -->
				<input type="checkbox" id="gradient-color-picker-checkbox-<?php echo esc_attr( $this->id ); ?>" <?php checked( esc_attr( $values[2] ), 'true' ); ?> />
			</label>
		</p>

		<script>
			jQuery( function( $ ) {
				'use strict';

				/************ On Load Events ************/
				// Display or hide the gradient controls (second color and the range control)
				$( '.hide-non-gradient-<?php echo esc_attr( $this->id ); ?>' ).toggle( 'true' === "<?php echo $values[2] ?>" );

				// Saving settings for customizer via JS wp.customize
				var saveSettings = function( val ) {
					wp.customize( '<?php echo esc_js( $this->id ); ?>', function( obj ) {
						obj.set( val );
					} );
				};

				// Convert input fields to color pickers
				$('.gradient-color-picker').wpColorPicker( {
					// A callback to fire whenever the color changes to a valid color
					change: function(event, ui){
						var currentValues;

						switch( event.target.id ) {
							case 'first-color-<?php echo esc_attr( $this->id ); ?>':
								currentValues = ui.color.toString() + ', ' + $( '#second-color-<?php echo esc_attr( $this->id ); ?>' ).val() + ', ' + $( '#gradient-color-picker-checkbox-<?php echo esc_attr( $this->id ); ?>' ).prop('checked') + ', ' + $('#range-<?php echo esc_attr( $this->id ); ?>').val();
								break;
							case 'second-color-<?php echo esc_attr( $this->id ); ?>':
								currentValues = $( '#first-color-<?php echo esc_attr( $this->id ); ?>' ).val() + ', ' + ui.color.toString() + ', ' + $( '#gradient-color-picker-checkbox-<?php echo esc_attr( $this->id ); ?>' ).prop('checked') + ', ' + $('#range-<?php echo esc_attr( $this->id ); ?>').val();
								break;
						}

						saveSettings( currentValues );
					},
				} );

				// Display/hide and save gradient controls on checkbox click
				$('#gradient-color-picker-checkbox-<?php echo esc_attr( $this->id ); ?>').click(function (e) {
					$(".hide-non-gradient-<?php echo esc_attr( $this->id ); ?>").toggle(this.checked);

					saveSettings( $( '#first-color-<?php echo esc_attr( $this->id ); ?>' ).val() + ', ' + $( '#second-color-<?php echo esc_attr( $this->id ); ?>' ).val() + ', ' + this.checked + ', ' + $('#range-<?php echo esc_attr( $this->id ); ?>').val() );
				});

				// Save updated gradient angle value.
				$('#range-<?php echo esc_attr( $this->id ); ?>').change(function(event) {
					saveSettings( $( '#first-color-<?php echo esc_attr( $this->id ); ?>' ).val() + ', ' + $( '#second-color-<?php echo esc_attr( $this->id ); ?>' ).val() + ', ' + $( '#gradient-color-picker-checkbox-<?php echo esc_attr( $this->id ); ?>' ).prop( 'checked' ) + ', ' + event.currentTarget.value );
				});

			} );
		</script>
	<?php
	}
}