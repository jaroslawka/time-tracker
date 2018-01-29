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
        
        $IssueState = $this->get('app.model.issue_state');
        $user = $this->get('app.model.user');
        
        $userId = $user->getLoggedUserId();
        $time = $request->get('time');
        $state = $request->get('state');
        $description = $request->get('description');
        $error = $request->get('error');

        $issue = $IssueState->getIssueInProgressOrPaused($userId);

        if (!empty($issue)) {
            $time = $IssueState->getCalculatedTimeForIssue($issue);
            $state = $issue->getState();
            if(!$description){
                $description = $issue->getDescription();
            }
        }

        return $this->render('default/index.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                'time' => $time,
                'state' => $state,
                'description' => $description,
                'error' => $error
        ]);
    }
}
