PDF Template
============

This Typo3 Extension allows for Opening an Existing PDF writing some text into it.
and store it into another PDF file. this then might be attached to an email or presented the user for download.

This extension commes in with an Typo3 Class for Extension Developers and an content-Object for writing data with Typoscript.

Content Object - The Easy way
-----------------------------

USAGE: 

1. include the Static Template.
2. configure your Typoscript Object see below

	10  < plugin.Tx_PdfTemplate_cObj
	10 {
		templatePdf = fileadmin/dgfb-pdf/dgfb-anmeldung-2013-einzug.pdf
		renderedPdfStorageFolder = fileadmin/dgfb-pdf/temp/
		fileNameFormat = ###YYYY###-###MM###-###DD###_{TSFE:id}_###HASH32###
		fileNameFormat.insertData = 1
		renderConfiguration {
			# Page 1
			1 {
				# first elemen on Page
				1 {
					lineHeight = 1.5 
					size = 30
					lines.1 = HELLO WORLD
					X = 10
					Y = 10
				}
			}
		}
	} 

most of the properties are string/stdWrap 


Using the Class in your Extension
---------------------------------

Class Name: Tx_Pdftemplate_Utility_Pdf_PdfTemplate

Public Functions: 
	/**
	 * loads a PDF as Template
	 * @param  string $filename the pdf file to open
	 * @return integer           returns the pagecount of the Open Document
	 */
	public function loadPDF($filename)

	/**
	 * writes the the Generated PDF to an file
	 * @param  string $filename filename to Write
	 * @return void
	 */
	public function writeAndClose($filename)

	/**
	 * loads the next Page for Processing.
	 * @return mixed returns the current pagenumber or false
	 */
	public function nextPage()

	/**
	 * sets the lineheigt in em
	 * @param float $lineheight the line height
	 */
	public function setLineHeight($lineheight)

	/**
	 * sets the font to be useds be aware your are limite to fonts included by fpdf
	 * @param  string $fontname the fontname 
	 * @return void     
	 */
	public function setfont($fontname)

	/**
	 * sets the fontsize in pt
	 * @param float $fontSize the fontsize
	 */
	public function setFontSize($fontSize)

	/**
	 * renders a text block 
	 * @param  float $x     the X postion
	 * @param  float $y     the Y position
	 * @param  mixed $lines string/array of lines to be rendered
	 * @return void       
	 */
	public function renderText($x,$y,$lines)

