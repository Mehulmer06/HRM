<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// load Composer’s autoloader
require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf {
    protected $ci;
    protected $dompdf;

    public function __construct() {
        // get CI’s superobject
        $this->ci = &get_instance();

        // set Dompdf options
        $options = new Options();
        $options->set('isRemoteEnabled', true); // allow external images/css
        $options->set('defaultFont', 'helvetica');

        // instantiate
        $this->dompdf = new Dompdf($options);
    }

    /**
     * Load a CI view into Dompdf
     *
     * @param string $view   Path to view file (e.g. 'reports/my_pdf')
     * @param array  $data   Data to pass to view
     * @param string $paper  Paper size, e.g. 'A4'
     * @param string $orientation 'portrait' or 'landscape'
     */
    public function load_view($view, $data = [], $paper = 'A4', $orientation = 'portrait') {
        // render CI view as HTML
        $html = $this->ci->load->view($view, $data, true);

        // load into Dompdf
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper($paper, $orientation);
        $this->dompdf->render();
    }

    /**
     * Stream the PDF to browser
     *
     * @param string  $filename   e.g. 'report.pdf'
     * @param boolean $download   true = force download, false = render inline
     */
    public function stream($filename = 'document.pdf', $download = true) {
        $this->dompdf->stream($filename, [
            "Attachment" => (bool)$download
        ]);
    }
}
