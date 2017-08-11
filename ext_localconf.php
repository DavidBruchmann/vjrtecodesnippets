<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
require_once(t3lib_extMgm::extPath('vjrtecodesnippets').'class.user_vjrtecodesnippets.php');

	t3lib_extMgm::addPageTSConfig('
	
		# Add ability for <code> element
		# if you like to change the tag name you can define some TypoScript analogical to this one
		# keep in mind to edit the TypoScript template configuration as well
		RTE.default {
		
			proc {
				allowTags := addToList(code)
				allowTagsOutside := addToList(code)
		
				entryHTMLparser_db {
					allowTags < RTE.default.proc.allowTags
					allowTagsOutside < RTE.default.proc.allowTagsOutside
				}
			
				HTMLparser_rte {
					allowTags < RTE.default.proc.allowTags
					allowTagsOutside < RTE.default.proc.allowTagsOutside
				}
		
			}
		
			## Additional Buttons for RTE
			showButtons := addToList(user)
		
			#
			# Userelement <code>
			#
			userElements.10.7 = SourceCode
			userElements.10.7 {
				description = Syntax highlighting with GeSHi
				mode = wrap
				content = <code language="php" extralines="" startline="1" url="" downloadtitle="">|</code>
			}
		}
		
		RTE.default.FE < RTE.default
	');

?>