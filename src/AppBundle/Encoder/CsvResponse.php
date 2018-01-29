<?php
namespace AppBundle\Encoder;

use Symfony\Component\HttpFoundation\Response;

class CsvResponse extends Response
{

    private $data;
    private $filename;

    public function __construct($filename, $data = array(), $status = Response::HTTP_OK, $headers = array())
    {
        parent::__construct('', $status, $headers);
        $this->filename = $filename;
        $this->encode($data);
        $this->serve();
    }

    private function encode(array $data)
    {
        $handle = fopen('php://temp', 'w+');
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $this->data = stream_get_contents($handle);
        fclose($handle);
    }

    private function serve()
    {
        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->filename));
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/csv');
        }

        return $this->setContent($this->data);
    }
}
