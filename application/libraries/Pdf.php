<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter PDF Library
 *
 * Generate PDF's in your CodeIgniter applications.
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Chris Harvey
 * @license			MIT License
 * @link			https://github.com/chrisnharvey/CodeIgniter-PDF-Generator-Library
 */

require_once(dirname(__FILE__) . '/dompdf/autoload.inc.php');

use Dompdf\Dompdf;
use Dompdf\Options;
//use Dompdf\FontMetrics;

class Pdf extends Dompdf
{
	/**
	 * Get an instance of CodeIgniter
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function ci()
	{
		return get_instance();
	}

	/**
	 * Load a CodeIgniter view into domPDF
	 *
	 * @access	public
	 * @param	string	$view The view to load
	 * @param	array	$data The view data
	 * @return	void
	 */
	public function load_view($view, $data = array())
	{
		$html = $this->ci()->load->view($view, $data, TRUE);

		$this->load_html($html);
	}

	function createPDF($html, $filename='', $download=TRUE, $paper='A4', $orientation='portrait')
	{
		// Set options to enable embedded PHP 
		$options = new Options(); 

		$options->set('isPhpEnabled', true);
		$options->set('isRemoteEnabled', true);

		//$options->set('tempDir', __DIR__ . '/site_uploads/dompdf_temp');
		//$options->set('isRemoteEnabled', TRUE);
		//$options->set('debugKeepTemp', TRUE);
		//$options->set('chroot', '/'); // Just for testing :)
		//$options->set('isHtml5ParserEnabled', true);

		//$domPdf->setOptions($options);

		$dompdf = new DOMPDF($options);

		$dompdf->getOptions()->setChroot(FCPATH."assets/uploads/images/");

		$contxt = stream_context_create([ 
					'ssl' => [ 
						'verify_peer' => FALSE, 
						'verify_peer_name' => FALSE,
						'allow_self_signed'=> TRUE
					] 
				]);

		//echo '<pre> After $options :: '; print_r($dompdf->getOptions()); die;

		$dompdf->setHttpContext($contxt);

		$dompdf->load_html($html);

		$dompdf->set_paper($paper, $orientation);

		// Render the HTML as PDF
		$dompdf->render();

		// Instantiate canvas instance
		$canvas = $dompdf->getCanvas();

		// Get height and width of page
		$w = $canvas->get_width();
		$h = $canvas->get_height();

		// Specify oogo image
		/*$logo_imageURL = FCPATH.'assets/uploads/images/143b8-f0b8b-86.jpg';
		$logo_imgWidth = 20;
		$logo_imgHeight = 20;

		// Specify logo image horizontal and vertical position 
		$x = (($w-$logo_imgWidth)/5);
		$y = (($h-$logo_imgHeight)/7);

		// Add logo image to the pdf 
		$canvas->image($logo_imageURL, $x, $y, $logo_imgWidth, $logo_imgHeight);*/

		// Specify watermark image 
		$imageURL = FCPATH.'assets/uploads/images/sodapdf-converted.jpg';
		$imgWidth = 400; 
		$imgHeight = 400;

		// Set text opacity 
		$canvas->set_opacity(.5);

		// Specify watermark image horizontal and vertical position 
		$x = (($w-$imgWidth)/2);
		$y = (($h-$imgHeight)/4);

		// Add watermark image to the pdf 
		$canvas->image($imageURL, $x, $y, $imgWidth, $imgHeight);

		// Specify watermark image 
		//$signature_imageURL = 'https://books.itbsh.com/assets/uploads/images/signature.png';
		//$signature_imageURL = URL_PUBLIC_UPLOADS.'images/NicePng_dan-howell-png_1667011.png';
		$signature_imageURL = FCPATH.'assets/uploads/images/sign.jpg';

		$signature_imgWidth = 60; 
		$signature_imgHeight = 40;

		//$canvas->set_opacity(.5);

		// Specify signature image image horizontal and vertical position 
		$x = (($w-$signature_imgWidth)/3);
		$y = (($h+$signature_imgHeight)/2);

		// Add signature image to the pdf 
		$canvas->image($signature_imageURL, $x, $y, $signature_imgWidth, $signature_imgHeight);

		if($download)
			$dompdf->stream($filename.'.pdf', array('Attachment' => 1));
		else
			$dompdf->stream($filename.'.pdf', array('Attachment' => 0));
	}
}