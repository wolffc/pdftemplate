<?php

########################################################################
# Extension Manager/Repository config file for ext: "pdftemplate"
#
# Auto generated by Extension Builder 2013-07-11
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'PDF Template',
	'description' => 'This Extension Allows you to Open an PDF Document as Template Write in some Text an store it to an new Location.

this can be done via a cObject with Typoscript or via an Extbase Class.
',
	'category' => 'fe',
	'author' => 'chris Wolff',
	'author_email' => 'chris@connye.com',
	'author_company' => '',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.1',
	'constraints' => array(
		'depends' => array(
			'extbase' => '1.3',
			'fluid' => '1.3',
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>