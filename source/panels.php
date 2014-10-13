<?php

/*
	Panel controler class.
*/

class PanelControler {
	
	function PanelControler($parent) {
		global $t, $cfg;
		
		foreach(array('left', 'right') as $side) {
			
			$cap_side = ucfirst($side);
			
			$t->set_file("{$side}_panels_template", "panel_template_{$side}.ihtml");
			$t->set_block("{$side}_panels_template", "{$side}_panels_template");
			$t->set_block("{$side}_panels_template",  "{$cap_side}PanelTemplateBlock", $cap_side{0} . "PTBlock");
			
			foreach ($cfg["{$side}_panels_list"] as $panel) {
				
				$t->set_file("panel_{$panel}_master", "panel_{$panel}_master.ihtml");
				$t->set_block("panel_{$panel}_master", "panel_{$panel}_master");
				
				$this->load_panel($panel, "panel_{$panel}_master");
				
				$t->parse("{$cap_side}PanelTemplateBlock", "panel_{$panel}_master");
				$t->parse($cap_side{0} . "PTBlock", "{$cap_side}PanelTemplateBlock", TRUE);
			}
		}
	}
	
	function load_panel($name, $parent) {
		global $t, $cfg, $lang, $core_db;
		include "./source/panels/{$name}.php";
	}
}

?>