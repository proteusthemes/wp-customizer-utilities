# WP Customizer Utilities

Custom WordPress customizer controls and settings for better using experience and for more advanced controls.

### Controls

- #### Layout Builder

  [jQuery UI slider](https://jqueryui.com/slider/) with an option how many handles you want to control in that slider. Useful for creating dynamic layouts, for example for the footer, where user can congigure how many columns they want and how wide each of these columns will be.

- #### Gradient

  Control for the CSS gradient (WP has only support for solid colors by default).

### Settings

- #### Dynamic CSS

  Create Dynamic CSS by declaring the selectors (for example for background colors etc.) when defining a setting. Has build-in support for some filters and media queries.

  **Use:**

  ```php
  $wp_customize->add_setting( new ProteusThemes\CustomizerUtils\Setting\DynamicCSS( $wp_customize, 'nav_bg', array(
    'default' => '#f7f7f7',
    'css_map' => array(
      'background-color' => array(
        '.sel;',
        '.sel .sub-menu',
      ),
      'border-bottom-color|darken(3)' => array(
        '.sel',
      ),
      'border-color' => array(
        '.sel .sub-menu > li:first-of-type > a|@media (min-width: 992px)',
      )
    )
  ) ) );
  ```

## Installation

Install it via [Composer](https://getcomposer.org/) and [Packagist](https://packagist.org/). Start using the classes and they will be auto-loaded (PSR-4).