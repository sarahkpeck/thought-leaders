<?php
class OptimizePress_Blog_Autosave_Module extends OptimizePress_Modules_Base {

    function __construct($config = array())
    {
        parent::__construct($config);
        // if first time, enable autosave by default
        $autosaveOption = unserialize(get_option(OP_SN . '_autosave'));
        if (empty($autosaveOption)) {
            $autosave['enabled'] = 'Y';
            $this->update_option('autosave', $autosave);
        }
    }
	
	function display($section_name = '', $return = false, $add_to_config = array()){
		/**/
	}

	function display_settings($section_name,$config=array(),$return=false){ 
		/**/
	?>
    <div class="op-bsw-grey-panel-content op-bsw-grey-panel-no-sidebar">
		<p class="op-micro-copy"><?php _e('Use this option to turn off the autosave feature inside the LiveEditor. This is <strong>NOT recommended</strong> and for advanced users only. Turning off this feature will <strong>prevent revisions of your pages from being saved</strong>, so please use caution if you use this option as your pages will not be saved unless you save them manually.',OP_SN) ?></p>
    </div>
    <?php	
	}
	
	function save_settings($section_name,$config=array(),$op){
		$autosave = array(
			'enabled' => op_get_var($op,'enabled','N')
		);
		$this->update_option('autosave',$autosave);
	}
}