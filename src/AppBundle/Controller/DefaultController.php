<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $error = $request->get('error');
        $issueState = $this->get('app.model.issue_state');
        if (!$error) {
            $issueState->getError();
        }

        return $this->render('default/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'time' => $issueState->getTime(),
                'state' => $issueState->getState(),
                'description' => $issueState->getDescription(),
                'error' => $error
        ]);
    }
}
