<?php if (!defined('ABSPATH')) {
/*
 * @Author: Qinver
 * @Url: dkewl.com
 * @Date: 2021-04-11 21:36:20
 * @LastEditTime: 2022-04-24 20:50:29
 */
  die;
} // Cannot access directly.

/**
 *
 * Field: accordion
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */

if (!class_exists('CSF_Field_accordion')) {
  class CSF_Field_accordion extends CSF_Fields
  {

    public function __construct($field, $value = '', $unique = '', $where = '', $parent = '')
    {

      parent::__construct($field, $value, $unique, $where, $parent);
    }

    public function render()
    {

      $unallows = array('accordion');

      echo $this->field_before();

      echo '<div class="csf-accordion-items">';

      foreach ($this->field['accordions'] as $key => $accordion) {

        echo '<div class="csf-accordion-item">';

        $icon = (!empty($accordion['icon'])) ? 'csf--icon ' . $accordion['icon'] : 'csf-accordion-icon fas fa-angle-right';

        echo '<h4 class="csf-accordion-title">';
        echo '<i class="' . esc_attr($icon) . '"></i>';
        echo $accordion['title'];  //修改此处
        echo '</h4>';

        echo '<div class="csf-accordion-content">';

        foreach ($accordion['fields'] as $field) {

          if (in_array($field['type'], $unallows)) {
            $field['_notice'] = true;
          }

          $field_id      = (isset($field['id'])) ? $field['id'] : '';
          $field_default = (isset($field['default'])) ? $field['default'] : '';
          $field_value   = (isset($this->value[$field_id])) ? $this->value[$field_id] : $field_default;
          $unique_id     = (!empty($this->unique)) ? $this->unique . '[' . $this->field['id'] . ']' : $this->field['id'];

          CSF::field($field, $field_value, $unique_id, 'field/accordion');
        }

        echo '</div>';

        echo '</div>';
      }

      echo '</div>';

      echo $this->field_after();
    }
  }
}
