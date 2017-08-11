<?php

/***************************************************************
*  Copyright notice
*  
*  (c) 2007 Vincent Tietz (vincent.tietz@vj-media.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/** 
 * @author	Vincent Tietz <vincent.tietz@vj-media.de>
 * @author	Jan-Philipp Halle <typo3@jphalle.de>
 */

class user_vjrtecodesnippets {


	function loadGeSHi() {
		if(t3lib_extMgm::isLoaded('geshilib')) {
			require_once(t3lib_extMgm::siteRelPath('geshilib').'res/geshi.php');
			return true;
		}

		if(t3lib_extMgm::isLoaded('jph_codesnippets')) {
			require_once(t3lib_extMgm::siteRelPath('jph_codesnippets').'res/geshi.php');
			return true;
		}
		return false;
	}

	/**
	  * This function uses GeSHi to highlight the syntax of a given content.
	  * Some code is taken from jph_codesnippets by Jan-Philipp Halle <typo3@jphalle.de>
	  * @param String Content to highlight
	  * @param Array Configuration
	  * @return String highlighted content
	  */
	function highlight($content, $conf) {

			// include GeSHi library
		if(!$this->loadGeSHi())
			return 'GeSHi not found. Please install the extension \'geshilib\' or \'jph_codesnippets\'.';
				
		$this->conf = $conf;
		
			// convert <br> to \n
		if($this->conf['convertBR2LBR'])
			$content = eregi_replace('<br />|<br>', chr(10), $content);
		
			// convert spaces to tabs
		if($this->conf['convertSpace2Tab']) {
			$content = eregi_replace('(\&nbsp; ?){'.($this->conf['convertSpace2Tab']).'}', chr(9), $content);		
		}
		
			// some configuration for geshi is taken from attributes of the code-tag
		$parameters = $this->cObj->parameters;
		
			// imports a given url or uses the content of <code>
		$content = $parameters['url'] ? t3lib_div::getURL($parameters['url']) : $content;

			// nothing to do
		if ($content == '') 
			return '';
		

			// default language is php
		$language = $parameters['language'] ? $parameters['language'] : 'php';

			// create Geshi object
		$geshi = new Geshi($content, $language, '');
		
			// Setting the start number for line numbering, if you only
			// want to display a part of a script.
		$start_line = $parameters['startline'];
		if ($start_line == '' || !is_int(@intval($start_line))) $start_line = 1;
		$geshi->start_line_numbers_at($start_line);

			// Mark lines with high importance or which you explain in your text.
		$lines_extra = t3lib_div::intExplode(',', $parameters['extralines']);
		if (($start_line > 1) && (is_array($lines_extra))) {
			for ($i = 0; $i < count($lines_extra); $i++) {
				$lines_extra[$i] = $lines_extra[$i] - $start_line + 1;		
			}
		}

		$geshi->highlight_lines_extra($lines_extra);




			// configuration by typoscript (done originally by flexform conf in tx_jphcodesnippets_pi)
			
			// headerstyle is pre by default
		$header_type = $this->conf['headerDiv'] ? $geshi->set_header_type(GESHI_HEADER_DIV) : $geshi->set_header_type(GESHI_HEADER_PRE);


		if (preg_match('/1|true|TRUE/', $this->conf['enableClasses']))
			$geshi->enable_classes();

		$geshi->set_overall_class($this->conf['overallClass']);
		$geshi->set_overall_id($this->conf['overallId']);

		$style = $this->conf['overallStyle'];

		$preserve_defaults = false;

		if (preg_match('/1|true|TRUE/', $this->conf['overallStyleDefaults']))
			$preserve_defaults = true;

		if ($style <> '' && $preserve_defaults == false) { 
			$geshi->set_overall_style($style);
		} elseif ($style <> '' && $preserve_defaults == true) {
			$geshi->set_overall_style($style, $preserve_defaults);
		}

		$style_1 = $this->conf['lineStyle1'];
		$style_2 = $this->conf['lineStyle2'];

		$preserve_defaults = false;

		if (preg_match('/1|true|TRUE/', $this->conf['lineStyleDefaults']))
			$preserve_defaults = true; 

		$geshi->set_line_style($style_1, $style_2, $preserve_defaults);

		$lines_extra_style = $parameters['linesextrastyle'] ? $parameters['linesextrastyle'] : $this->conf['linesExtraStyle'];
		if ($lines_extra_style <> '') $geshi->set_highlight_lines_extra_style($lines_extra_style);

		$tab_width = $this->conf['tabWidth'];
		if (is_int(@intval($tab_width)))
			$geshi->set_tab_width($tab_width);
		
			// Activate line numbering.
			// You can set special styling every n-th line only in fancy mode.
		$fancy_lines = $parameters['nth_row'] ? $parameters['nth_row'] : $this->conf['nth_row'];
		$lineNumbers = $parameters['linenumbers'] ? $parameters['linenumbers'] : $this->conf['lineNumbers'];
		
		if ($fancy_lines == '' || !is_int(@intval($fancy_lines)))
			$fancy_lines = 2;
		if ($lineNumbers == 1)
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
		if ($lineNumbers == 2)
			$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, $fancy_lines);

			// Activate/deactivate function linking.
			// parameter overrides typoscript conf
		$functionLinking = $parameters['functionlinking'] ? $parameters['functionlinking'] : $this->conf['functionLinking'];
		$geshi->enable_keyword_links($functionLinking);

	
		$result = $geshi->parse_code();
	
	
			// send all parameters to cObject's data
		$this->cObj->data = $parameters;
		
			// remove line breaks of GeSHi
		if($this->conf['removeGeSHiLBR'])
			$result = eregi_replace("\n", '', $result);
		
		return $this->cObj->stdWrap($result, $this->conf['stdWrap.']);
	}
}


if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vjrtecodesnippets/class.user_vjrtecodesnippets.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/vjrtecodesnippets/class.user_vjrtecodesnippets.php"]);
}


?>