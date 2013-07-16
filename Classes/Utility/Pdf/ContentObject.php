<?php

	/**
	 * This class acts as an cObject in typoscript sense and uses PDFTemplate as backend for rendering the PDF
	 */
	// TODO: set Output options (eg not full filename)
	class Tx_Pdftemplate_Utility_Pdf_ContentObject {
		/**
		 * The Html Text the plugin returns usaly the filename
		 * @var string
		 */
		protected  $content ='';
		/**
		 * the Typoscript Configuration array 
		 * @var array
		 */
		protected  $typoscript;
		/**
		 * The current Page we are Working on
		 * @var integer
		 */
		protected $page = 0;

		protected $pageCount = 0;

		protected $objectManager;
		/**
		 * [$pdf description]
		 * @var [type]
		 */
		protected $pdfTemplate;

		protected $contentObject;

		/**
		 * Intializize the Object Data
		 * @return NULL
		 */
		public function init(){
			$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
			$this->contentObject = $this->objectManager->get('tslib_cObj');
		
			// Just fetch the dummy class with a prefix name as this makes shure the ext_autoload file is loaded on Typo3 4.5 (fixed in 4.6)
			$this->objectManager->get('tx_Pdftemplate_autoloaderdummy');

			$this->pdfTemplate= $this->objectManager->get('Tx_Pdftemplate_Utility_Pdf_PdfTemplate');
		}

		/**
		 * Intializizes the Typoscript Configuration Replaces and Verfies the Settings
		 * @return boolean returns true if everything is fine false if an error occured writes error message to $this->content
		 */
		protected function initTyposcriptConfiguration(){

			/* TemplatePdf */
			$this->typoscript['templatePdf'] = $this->contentObject->stdWrap($this->typoscript['templatePdf'],$this->typoscript['templatePdf.']);
			$this->typoscript['templatePdf'] = t3lib_div::getFileAbsFileName($this->typoscript['templatePdf']);
			if(empty($this->typoscript['templatePdf'])){
				$this->showError('empty template file Given check your typoscript configuration','templatePdf');
			 return false;
			}

			/* renderedPdfStorageFolder */
			$this->typoscript['renderedPdfStorageFolder'] = $this->contentObject->stdWrap($this->typoscript['renderedPdfStorageFolder'],$this->typoscript['renderedPdfStorageFolder.']);
			if(empty($this->typoscript['renderedPdfStorageFolder'])){
				$this->showError('no Storage FolderGiven','renderedPdfStorageFolder');
				return false;
			}

			$this->typoscript['fileNameFormat'] = $this->contentObject->stdWrap($this->typoscript['fileNameFormat'],$this->typoscript['fileNameFormat.']);
			if(empty($this->typoscript['fileNameFormat'])){
				$this->showError('no Fileformat String given','fileNameFormat');
				return false;
			} 
			
			$fileformatReplace = array(
					'###DD###'=>date('d'), 
					'###D###'=>date('j'), 
					'###MM###' =>date('m'),
					'###M###' =>date('m'),
					'###YYYY###' => date('Y'),
					'###YY###' => date('y'),
					'###MIN###' => date('i'),
					'###HH###' => date('H'),
					'###HASH8###' => substr(md5(time().microtime().mt_rand()), 0,8),
					'###HASH16###' => substr(md5(time().microtime().mt_rand()), 0,16),
					'###HASH32###' => md5(time().microtime().mt_rand()),
					'###HASH64###' => md5(time().microtime().mt_rand()) . md5(time().microtime().mt_rand()),
				);
			$this->typoscript['fileNameFormat'] = strtr($this->typoscript['fileNameFormat'],$fileformatReplace) .'.pdf';
			if($this->typoscript['fileNameFormat']=='.pdf'){
				$this->showError('invalid Filename Format');
				return false;
			} 


			if(!array_key_exists('renderConfiguration.', $this->typoscript) or !is_array($this->typoscript['renderConfiguration.'])){
				$this->showError('no Render configuration','renderConfiguration');
				return false;
			} 
			
			switch($this->typoscript['returnFormat']) {
				case 'filename':
				case 'relative':
					break; 
				default:
					$this->typoscript['returnFormat'] = 'absolute';
			}

			// Every thing okay
			return true;
		}

		/**
		 * Starts rendering of the Content
		 * @param  string $content    html content should be empty
		 * @param  string $typoscript the typoscript configuration
		 * @return string             the html output usaly the link to the file or the error message
		 */
		public function main($content,$typoscript){

			$this->typoscript = $typoscript;
			$this->content = $content;
			$this->init();

			if($this->initTyposcriptConfiguration()){
				$this->pageCount = $this->pdfTemplate->loadPDF($this->typoscript['templatePdf']);
				if($this->pageCount == 0){
					$this->content .=' Page Count 0';
					return $this->content;
				}
				$renderConfiguration = $this->typoscript['renderConfiguration.'];
				for($this->page; $this->page <= $this->pageCount; $this->page++){
					if(array_key_exists($this->page.'.', $renderConfiguration) and is_array($renderConfiguration[$this->page.'.'])){
						$this->renderPage($renderConfiguration[$this->page.'.']);
					}
					// advance a Page in the PDF;
					$this->pdfTemplate->nextPage();
				}

				$output = array();
				$output['filename'] = $this->typoscript['fileNameFormat'];
				$output['relative'] = $this->typoscript['renderedPdfStorageFolder'] . $output['filename'];
				$output['absolute'] = t3lib_div::getFileAbsFileName($output['relative']);

				$this->pdfTemplate->writeAndClose($output[$this->typoscript['returnFormat']]);
				$this->content.= $output[$this->typoscript['returnFormat']];
			}
			return $this->content;
	  
		}

		/**
		 * Renders a Single Page by passing each element on page to renderElements
		 * @param  array $pageElements configuration array of the current page
		 * @return void
		 */
		protected function renderPage($pageElements){
			foreach($pageElements as $element){
				$this->renderElement($element);
			}
		}

		/**
		 * renders a single Text Element on page
		 * @param  array $element typoscript configuration Array for a text element on page
		 * @return void          
		 */
		protected function renderElement($element){
			if(!array_key_exists('lines.',$element)){
				return false; // no lines to be rendered return
			}
			$lines = array();
			foreach ($element['lines.'] as $theKey =>$theConf){
				if (intval($theKey) && !strstr($theKey, '.')){
					$lines[] = $this->contentObject->stdWrap($element['lines.'][$theKey],$element['lines.'][$theKey.'.']);
				}
			}

			$xPosition = intval($this->contentObject->stdWrap($element['X'],$element['X.']));
			$yPosition = intval($this->contentObject->stdWrap($element['Y'],$element['Y.']));
			$size = $this->contentObject->stdWrap($element['size'],$element['size.']);
			if(!empty($size)){
				$this->pdfTemplate->setFontSize($size);
			}
			$lineheight = $this->contentObject->stdWrap($element['lineheight'],$element['lineheight.']);
			if(!empty($lineheight)){
				$this->pdfTemplate->setLineheight($lineheight);
			}
			$this->pdfTemplate->renderText($xPosition,$yPosition,$lines);
		}

		/**
		 * This function Adds an ErrorMessage to the Output
		 * @return [type] [description]
		 */
		protected function showError($message,$title='Error'){
			$this->content .= '<div style="margin:1em; padding:1em; background:yellow; border:3px double red;">';
			$this->content .= '<strong>'. htmlentities($title) .'</strong>';
			$this->content .= '<p>'. htmlentities($message) .'</p>';
			$this->content .= '</div>';
		}

		
			
	}

?>