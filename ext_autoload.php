<?php
	$extensionPath = t3lib_extMgm::extPath('pdftemplate');

	return array(
		// 	this is empty class file for tricking the autloader on typo3 4.5 not needet on 4.6
		'tx_pdftemplate_autoloaderdummy' =>  $extensionPath .'Resources/Private/Php/autoloaderdummy.php',
		'tx_pdftemplate_utility_pdf_pdftemplate' =>      $extensionPath .'Classes/Utility/Pdf/PdfTemplate.php',

		'tfpdf' => $extensionPath .'Resources/Private/Php/Pdf/Tfpdf/tfpdf.php',
		'fpdf' => $extensionPath .'Resources/Private/Php/Pdf/Tfpdf/fpdf.php',
		'fpdi' => $extensionPath .'Resources/Private/Php/Pdf/Fpdi/fpdi.php',
		//tFPDF
	);
?>