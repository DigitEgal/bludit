<?php

class pluginsimpleMDE extends Plugin {

	private $loadOnController = array(
		'new-content',
		'edit-content'
	);

	public function init()
	{
		$this->dbFields = array(
			'tabSize'=>'2',
			'toolbar'=>'"bold", "italic", "heading", "|", "quote", "unordered-list", "|", "link", "image", "code", "horizontal-rule", "|", "preview", "side-by-side", "fullscreen"',
			'spellChecker'=>true
		);
	}

	public function form()
	{
		global $language;

		$html  = '<div>';
		$html .= '<label>'.$language->get('toolbar').'</label>';
		$html .= '<input name="toolbar" id="jstoolbar" type="text" value="'.$this->getValue('toolbar').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$language->get('tab-size').'</label>';
		$html .= '<input name="tabSize" id="jstabSize" type="text" value="'.$this->getValue('tabSize').'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$language->get('spell-checker').'</label>';
		$html .= '<select name="spellChecker">';
		$html .= '<option value="true" '.($this->getValue('spellChecker')===true?'selected':'').'>'.$language->get('enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('spellChecker')===false?'selected':'').'>'.$language->get('disabled').'</option>';
		$html .= '</select>';
		$html .= '</div>';

		return $html;
	}

	public function adminHead()
	{
		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		// Include plugin's CSS files
		$html  = $this->includeCSS('simplemde.min.css');
		$html .= $this->includeCSS('bludit.css');
		return $html;
	}

	public function adminBodyEnd()
	{
		global $language;

		if (!in_array($GLOBALS['ADMIN_CONTROLLER'], $this->loadOnController)) {
			return false;
		}

		// Spell Checker
		$spellCheckerEnable = $this->getValue('spellChecker')?'true':'false';

		// Include plugin's Javascript files
		$html  = $this->includeJS('simplemde.min.js');
		$html .= '<script>'.PHP_EOL;
		$html .= 'var simplemde = null;'.PHP_EOL;

		// Include add content to the editor
		$html .= 'function addContentSimpleMDE(content) {
				var text = simplemde.value();
				simplemde.value(text + content + "\n");
				simplemde.codemirror.refresh();
			}'.PHP_EOL;

		// Function required for Autosave function
		// Returns the content of the editor
		$html .= 'function editorGetContent(content) {
			return simplemde.value();
		}'.PHP_EOL;

		// Function required for Media Manager
		// Insert an image on the editor in the cursor position
		$html .= 'function editorInsertMedia(filename) {
				addContentSimpleMDE("!['.$language->get('Image description').']("+filename+")");
			}'.PHP_EOL;

		$html .= '$(document).ready(function() { '.PHP_EOL;
		$html .= '
		simplemde = new SimpleMDE({
				element: document.getElementById("jseditor"),
				status: false,
				toolbarTips: true,
				toolbarGuideIcon: true,
				autofocus: false,
				placeholder: "'.$language->get('content-here-supports-markdown-and-html-code').'",
				lineWrapping: true,
				autoDownloadFontAwesome: false,
				indentWithTabs: true,
				tabSize: '.$this->getValue('tabSize').',
				spellChecker: '.$spellCheckerEnable.',
				toolbar: ['.Sanitize::htmlDecode($this->getValue('toolbar')).',
					"|",
					{
					name: "pageBreak",
					action: function addPageBreak(editor){
						var cm = editor.codemirror;
						output = "\n'.PAGE_BREAK.'\n";
						cm.replaceSelection(output);
						},
					className: "oi oi-crop",
					title: "'.$language->get('Pagebreak').'",
					}]
		});';
		$html .= '}); </script>';
		return $html;
	}
}
