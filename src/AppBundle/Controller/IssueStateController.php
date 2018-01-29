<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Model\IssueState;

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

        $state = $this->get('app.model.issue_state');
        $user = $this->get('app.model.user');

        $userId = $user->getLoggedUserId();
        $action = $request->get('action');
        $description = $request->get('description');

        $issue = $state->getIssueInProgressOrPaused($userId);

        if (!empty($issue)) {
            $id = $issue->getId();
            if (empty($description)) {
                $description = $issue->getDescription();
            }
            $time = $state->getCalculatedTimeForIssue($issue);
            $params = array('state' => $issue->getState(), 'time' => $time, 'description' => $description);
        } else {
            $params = array('description' => $description);
        }

        if (empty($id) && $action != 'start') {
            $params['error'] = IssueState::ERROR_NOT_FOUND;
        } elseif ($action === 'start') {
            if (empty($issue)) {
                $issue = $state->stateIssueStart($userId, $description);
                if (!empty($issue)) {
                    $timeBegin = $issue->getBegin()->getTimestamp();
                    if ($timeBegin) {
                        $time = time() - $timeBegin;
                        $params['state'] = IssueState::STATE_IN_PROGRESS;
                        $params['time'] = $time;
                    }
                }
            }
        } elseif ($action === 'continue') {
            $issue = $state->stateIssueContinue($id);
            if ($issue) {
                $time = $state->getCalculatedTimeForIssue($issue);
                $params['time'] = $time;
                $params['state'] = $issue->getState();
            }
        } elseif ($action === 'pause') {
            $state->stateIssuePause($id);
            $params['state'] = IssueState::STATE_PAUSED;
        } elseif ($action === 'stop') {
            if (empty($description)) {
                $params['error'] = IssueState::ERROR_DESCRIPTION_NOT_FOUND;
            } else {
                $state->stateIssueStop($id, $description);
                $params = [];
            }
        }

        return $this->redirectToRoute('homepage', $params, Response::HTTP_FOUND);
    }
}
