<?php
// Copyright 2014 SEEDPROD LLC (email : john@seedprod.com, twitter : @seedprod)
class SeedReduxFramework_license_check {

    /**
     * Field Constructor.
     *
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @since SeedReduxFramework 1.0.0
    */
    function __construct( $field = array(), $value ='', $parent ) {

        //parent::__construct( $parent->sections, $parent->args );
        $this->parent = $parent;
        $this->field = $field;
        $this->value = $value;

    }

    /**
     * Field Render Function.
     *
     * Takes the vars and outputs the HTML for the field in the settings
     *
     * @since SeedReduxFramework 1.0.0
    */
    function render() {

        if( !empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
            if (empty($this->field['args'])) {
                $this->field['args'] = array();
            }
            $this->field['options'] = $this->parent->get_wordpress_data($this->field['data'], $this->field['args']);
            $this->field['class'] .= " hasOptions ";
        }

    	if (empty($this->value) && !empty( $this->field['data'] ) && !empty( $this->field['options'] )) {
    		$this->value = $this->field['options'];
    	}

    	$placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';

        //if (isset($this->field['text_hint']) && is_array($this->field['text_hint'])) {
            $qtip_title = isset($this->field['text_hint']['title']) ? 'qtip-title="' . $this->field['text_hint']['title'] . '" ' : '';
            $qtip_text  = isset($this->field['text_hint']['content']) ? 'qtip-content="' . $this->field['text_hint']['content'] . '" ' : '';
        //}

        $readonly = isset($this->field['readonly']) ? ' readonly="readonly"' : '';

    	if ( isset( $this->field['options'] ) && !empty( $this->field['options'] ) ) {
    		$placeholder = (isset($this->field['placeholder']) && !is_array($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';
			foreach($this->field['options'] as $k => $v){
				if (!empty($placeholder)) {
					$placeholder = (is_array($this->field['placeholder']) && isset($this->field['placeholder'][$k])) ?	' placeholder="' . esc_attr($this->field['placeholder'][$k]) . '" ' : '';
				}
				echo '<label for="' . $this->field['id'] . '-text-'.$k.'"><strong>'.$v.'</strong></label> ';
				echo '<input ' . $qtip_title . $qtip_text . 'type="text" id="' . $this->field['id'] . '-text-' . $k . '" name="' . $this->field['name'] . '['.$k.']' . $this->field['name_suffix'] . '" ' . $placeholder . 'value="' . esc_attr($this->value[$k]) . '" class="regular-text ' . $this->field['class'] . '"'.$readonly.' /><br />';

			}//foreach

    	} else {

    		echo '<input ' . $qtip_title . $qtip_text . 'type="password" id="' . $this->field['id'] . '-text" name="' . $this->field['name'] . $this->field['name_suffix'] . '" ' . $placeholder . 'value="' . esc_attr($this->value) . '" class="regular-text ' . $this->field['class'] . '"'.$readonly.' />';
    	}
        echo "<button id='seed_cspv4_check_license' type='button' class='button-secondary'>".__('Check License','seedprod')."</button><br>";
        $msg = get_option('seedprod-coming-soon-pro_update_msg');
        echo "<div id='seed_cspv4_check_license_msg'>".$msg."</div>";

        $ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seed_cspv4_check_license','seed_cspv4_check_license'));
        echo '
        <script type="text/javascript">
        jQuery(document).ready(function($) {
        $("#seed_cspv4_check_license").click(function() {
        $("#seed_cspv4_check_license").prop("disabled", true);
        $("#seed_cspv4_check_license_msg").hide();
        apikey = $("#api_key-text").val();
        //if(apikey != ""){
        if(1 == 1){
        $.get("'.$ajax_url.'&apikey="+apikey, function(data) {
          var response = $.parseJSON(data);
          $("#seed_cspv4_check_license_msg").text(response.message);
          //console.log(response.message);
          $("#seed_cspv4_check_license_msg").fadeIn();
          $("#seed_cspv4_check_license").prop("disabled", false);
        });
        }else{
            $("#seed_cspv4_check_license_msg").show();
            $("#seed_cspv4_check_license").prop("disabled", false);
        }

        });
        });
        </script>
        ';
    }
}

?>
