<?php
class menu_admin{

    public function __construct(){

        if(is_admin() ){
    	    add_action('admin_menu', array($this, 'add_plugin_page'));
    	}
    }

    public function add_plugin_page(){
		add_options_page(
	           'Import Inspiration',
	           'Import Inspiration',
	           'manage_options',
	           'rp_ig_setting',
	           array($this, 'setting_form')
	       );
    }

    public function setting_form(){

        ?>
    	<div class="wrap">

			<h2>Import csv inspirations</h2>
			<p>
				<?php $csvExport = new CSVImport(); ?>
			</p>
    	</div>
    	<?php
    }
}

$ig = new menu_admin();
