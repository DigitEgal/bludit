<?php defined('BLUDIT') or die('Bludit CMS.');

// ============================================================================
// Check role
// ============================================================================

checkRole(array('admin'));

// ============================================================================
// Functions
// ============================================================================

// ============================================================================
// Main before POST
// ============================================================================
$plugin = false;
$pluginClassName = $layout['parameters'];

// Check if the plugin exists
if (isset($plugins['all'][$pluginClassName])) {
	$plugin = $plugins['all'][$pluginClassName];
} else {
	Redirect::page('plugins');
}

// Check if the plugin has the method form()
if (!method_exists($plugin, 'form')) {
	Redirect::page('plugins');
}

// ============================================================================
// POST Method
// ============================================================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Add to syslog
	$syslog->add(array(
		'dictionaryKey'=>'plugin-configured',
		'notes'=>$plugin->name()
	));

	// Call the method post of the plugin
	if ($plugin->post()) {
		Alert::set( $language->g('The changes have been saved') );
		Redirect::page('configure-plugin/'.$plugin->className());
	} else {
		Alert::set( $language->g('Complete all fields') );
	}
}

// ============================================================================
// Main after POST
// ============================================================================

// Title of the page
$layout['title'] = $language->g('Plugin').' - '.$plugin->name().' - '.$layout['title'];