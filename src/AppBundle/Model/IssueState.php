<?php
namespace AppBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Issue;
use AppBundle\Helper\TimeHelper;

class IssueState
{

    use TimeHelper;

    const STATE_IN_PROGRESS = 1;
    const STATE_PAUSED = 2;
    const STATE_END = 3;
    const ERROR_NOT_FOUND = 101;
    const ERROR_DESCRIPTION_NOT_FOUND = 102;

    private $manager;
    private $issueRepository;
    private $issue;
    private $userId;
    private $time;
    private $description;
    private $state;
    private $error;

    public function __construct(EntityManagerInterface $manager, User $user)
    {
        $this->manager = $manager;
        $this->userId = $user->getLoggedUserId();

        $this->issueRepository = $manager->getRepository('AppBundle:Issue');
        $this->issue = $this->issueRepository->getIssueInProgressOrPaused($this->userId);
        if (!empty($this->issue)) {
            $this->setDescription($this->issue->getDescription());
        }
    }

    public function issueStart()
    {
        if (empty($this->issue)) {
            $description = $this->getDescription() ?? '';
            $this->issue = $this->issueRepository->stateIssueStart($this->userId, $description);
            if (!empty($issue)) {
                $timeBegin = $issue->getBegin()->getTimestamp();
                if ($timeBegin) {
                    $time = $this->getCurrentDateTime()->getTimestamp() - $timeBegin;
                    $this->setState(IssueState::STATE_IN_PROGRESS);
                    $this->setTime($time);
                }
            }
        }
    }

    public function issuePause()
    {
        $state = $this->issueRepository->stateIssuePause($this->issue->getId());
        if (!empty($issue)) {
            $this->setState($issue->getState());
        }
    }

    public function issueContinue()
    {
        $issue = $this->issueRepository->stateIssueContinue($this->issue->getId());
        if (!empty($issue)) {
            $time = $this->getCalculatedTimeForIssue($issue);
            $this->setTime($time);
            $this->setState($issue->getState());
        }
    }

    public function issueStop()
    {
        $description = $this->getDescription();
        if (empty($description)) {
            $this->setError(self::ERROR_DESCRIPTION_NOT_FOUND);
        } else {
            $this->issueRepository->stateIssueStop($this->issue->getId(), $description, $this->getTime());
            $this->setTime(null);
            $this->setDescription(null);
            $this->setState(null);
        }
    }

    /**
     * Set time.
     *
     * @param int $time
     *
     * @return Issue
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Calculate how long the issue lasted (excluding breaks time)
     * 
     * @param Issue $issue
     * @return type
     */
    public function getCalculatedTimeForIssue(Issue $issue)
    {
        $breaksRepository = $this->manager->getRepository('AppBundle:Breaks');

        $timeBegin = $issue->getBegin()->getTimestamp();
        $breaksTotalTime = $breaksRepository->getBreaksTimeTotal($issue->getId());
        $time = $this->getCurrentDateTime()->getTimestamp() - $timeBegin - $breaksTotalTime;
        return $time;
    }

    /**
     * Get time.
     *
     * @return int
     */
    public function getTime()
    {
        if (!empty($this->issue)) {
            $this->time = $this->getCalculatedTimeForIssue($this->issue);

            return $this->time;
        } else {

            return null;
        }
    }

    /**
     * Set desciption.
     *
     * @param string|null $description
     *
     * @return Issue
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get desciption.
     *
     * @return string|null
     */
    public function getDescription()
    {
        if (!empty($this->description)) {

            return $this->description;
        }
        if (!empty($this->issue)) {
            $this->description = $this->issue->getDescription();

            return $this->description;
        } else {

            return null;
        }
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return Issue
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        if (!empty($this->issue)) {

            return $this->issue->getState();
        } else {

            return null;
        }
    }

    /**
     * Set error.
     *
     * @param int $error
     *
     * @return Issue
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error.
     *
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }
}
