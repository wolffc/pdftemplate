<?php
	class Tx_Pdftemplate_Utility_Pdf_PdfTemplate {
		/**
		 * The current Page we are Working on
		 * @var integer
		 */
		protected $page = 0;

		/**
		 * the page count of the current Open pdf file
		 * @var integer
		 */
		protected $pageCount = 0;
		/**
		 * the lineheigt in em
		 * @var float
		 */
		protected $lineheight = 1.5;

		/**
		 * the font name to be renderd
		 * @var string
		 */
		protected $fontname = 'Arial';

		/**
		 * fontsize
		 * @var float
		 */
		protected $fontSize = 8;

		/**
		 * @var Tx_Extbase_Object_ObjectManager
		 */
		protected $objectManager;
		/**
		 * pdf Writer Class
		 * @var fpdi
		 */
		protected $pdf;

		/**
		 * the currently Loaded Template File (pdf)
		 * @var string
		 */
		protected $templateFile;

		/**
		 * Injects the ObjectManager
		 * @param  Tx_Extbase_Object_ObjectManager $objectManager Extbase Object Manager
		 * @return void
		 */
		public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager){
			$this->objectManager = $objectManager;
		}

		/**
		 * injects the pdf writing class
		 * @param  fpdi   $pdf the pdfwriting class
		 * @return void
		 */
		public function injectPDF(fpdi $pdf){
			$this->pdf = $pdf;
		}

		/**
		 * loads a PDF as Template
		 * @param  string $filename the pdf file to open
		 * @return integer           returns the pagecount of the Open Document
		 */
		public function loadPDF($filename){
			$this->templateFile = $filename;
			$this->pdf->open(); // Start a New PDF
			$this->pageCount = $this->pdf->setSourceFile($this->templateFile); // Load the File
			$this->pdf->open(); // Start a New PDF
			$this->pdf->setFont($this->fontname);
    		$this->pdf->setFontSize($this->fontSize);
    		//$this->nextPage(); // opening first page
    		return $this->pageCount;
		}

		/**
		 * writes the the Generated PDF to an file
		 * @param  string $filename filename to Write
		 * @return void
		 */
		public function writeAndClose($filename){
			// FIXME: if we are not at the end of the file call ->nextPage() till we reached the end
			$this->pdf->Output($filename, 'F'); // The F means file
		}

		/**
		 * loads the next Page for Processing.
		 * @return mixed returns the current pagenumber or false
		 */
		public function nextPage(){
			if($this->page < $this->pageCount){
				$this->page++;
				$templateIndex = $this->pdf->importPage($this->page,'/MediaBox');
				$this->pdf->addPage();
      			$this->pdf->useTemplate($templateIndex, 0, 0);
				return $this->page;
			}else{
				return false;
			}
		}

		/**
		 * sets the lineheigt in em
		 * @param float $lineheight the line height
		 */
		public function setLineHeight($lineheight){
			$this->lineheight = $lineheight;
		}

		/**
		 * converts the em line height in an milimeter value
		 * @return float the line height in milimeter
		 */
		public function getLineHeightInMillimeter(){
			# 0.3527 DPT Punkt
			return $this->lineheight * 0.3527 * $this->fontSize;
		}

		/**
		 * sets the font to be useds be aware your are limite to fonts included by fpdf
		 * @param  string $fontname the fontname
		 * @return void
		 */
		public function setfont($fontname){
			$this->fontname = $fontname;
			if($this->pdf){
				$this->pdf->setFont($this->fontname);
			}
		}

		/**
		 * sets the fontsize in pt
		 * @param float $fontSize the fontsize
		 */
		public function setFontSize($fontSize){
			$this->fontSize = $fontSize;
			if($this->pdf){
				$this->pdf->setFontSize($this->fontSize);
			}
		}

		/**
		 * renders a text block
		 * @param  float $x     the X postion
		 * @param  float $y     the Y position
		 * @param  mixed $lines string/array of lines to be rendered
		 * @return void
		 */
		public function renderText($x,$y,$lines){
			if(!is_array($lines)){
				$lines = array($lines);
			}
			foreach($lines as $line){
				$line = utf8_decode($line);
				$this->pdf->text($x,$y,$line);
				$y += $this->getLineHeightInMillimeter();
			}
		}
	}
?>