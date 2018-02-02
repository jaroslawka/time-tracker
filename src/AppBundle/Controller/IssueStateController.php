<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Issue State controller.
 *
 * @Route("/issue")
 */
class IssueStateController extends Controller
{

    /**
     * Set state of issue
     *
     * @Route("/", name="issue_post")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $issueState = $this->get('app.model.issue_state');

        $action = $request->get('action');
        $description = $request->get('description');

        if (!empty($description)) {
            $issueState->setDescription($description);
        }

        switch ($action) {
            case 'start':
                $issueState->issueStart();
                break;
            case 'pause':
                $issueState->issuePause();
                break;
            case 'continue':
                $issueState->issueContinue();
                break;
            case 'stop':
                $issueState->issueStop();
                break;
        }

        $params = Array(
            'state' => $issueState->getState(),
            'time' => $issueState->getTime(),
            'description' => $issueState->getDescription(),
            'error' => $issueState->getError()
        );

        return $this->redirectToRoute('homepage', $params, Response::HTTP_FOUND);
    }
}
