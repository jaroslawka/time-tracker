<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Encoder\CsvResponse;

/**
 * Issue controller.
 *
 * @Route("/issue")
 */
class IssueExportController extends IssueController
{

    /**
     * Export records to CSV (currently filtered)
     *
     * @Route("/export/csv", name="export_csv")
     * @Method("GET")
     */
    public function exportAction(Request $request)
    {
        $grid = $this->get('app.model.grid');

        $datetime = new \DateTime();
        $datetimeString = $datetime->format('Y.m.d-H.i.s');
        $filename = 'time-tracker.' . $datetimeString . '.csv';

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Issue')->createQueryBuilder('e');
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        $data = $queryBuilder->getQuery()->getResult();

        $dataForCsv = $grid->prepareDateForCsv($data);

        $response = new CsvResponse($filename, $dataForCsv, Response::HTTP_OK);

        return $response;
    }
}
