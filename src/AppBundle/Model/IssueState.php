<?php
namespace AppBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Issue;
use AppBundle\Entity\Breaks;
use AppBundle\Helper\IssueHelper;

class IssueState
{
    use IssueHelper;

    const STATE_IN_PROGRESS = 1;
    const STATE_PAUSED = 2;
    const STATE_END = 3;
    const ERROR_NOT_FOUND = 101;
    const ERROR_DESCRIPTION_NOT_FOUND = 102;

    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get Issue in progress
     * @return object
     */
    public function getIssueInProgressOrPaused($userId)
    {

        try {
            $queryBuilder = $this->manager->getRepository('AppBundle:Issue')->createQueryBuilder('e');
            $result = $queryBuilder
                ->where('e.userId = :userId ')
                ->andWhere('e.state != :state')
                ->setParameter('userId', $userId)
                ->setParameter('state', self::STATE_END)
                ->getQuery()
                ->getSingleResult()
            ;
        } catch (\Exception $ex) {

            return false;
        }

        return $result;
    }

    /**
     * 
     * Create new issue (state in progress)
     * 
     * @param string $description
     * @return Issue|null
     */
    public function stateIssueStart(int $userId, string $description)
    {

        $dateTime = $this->getCurrentDateTIme();

        try {
            $issue = new Issue();
            $issue->setState(self::STATE_IN_PROGRESS);
            $issue->setUserId($userId);
            $issue->setBegin($dateTime);
            $issue->setDescription($description);
            $this->manager->persist($issue);
            $this->manager->flush();

            return $issue;
        } catch (\Exception $ex) {

            return null;
        }
    }

    /**
     * 
     * Set state Paused
     * 
     * @param int $id
     * @return Issue|null
     */
    public function stateIssuePause(int $id)
    {

        $dateTime = $this->getCurrentDateTIme();

        try {
            $this->manager->getConnection()->beginTransaction();

            $issue = $this->manager->getRepository('AppBundle:Issue')->find($id);
            $issue->setState(self::STATE_PAUSED);
            $this->manager->flush();

            $break = new Breaks();
            $break->setIssueId($issue->getId());
            $break->setBegin($dateTime);
            $this->manager->persist($break);
            $this->manager->flush();

            $this->manager->getConnection()->commit();

            return $issue;
        } catch (\Exception $ex) {

            $this->manager->getConnection()->rollBack();

            return null;
        }
    }

    /**
     * Set state In Progress (Continuation)
     * 
     * @param int $id
     * @return Issue|null
     */
    public function stateIssueContinue(int $id)
    {

        $dateTime = $this->getCurrentDateTIme();

        try {

            $this->manager->getConnection()->beginTransaction();

            $issue = $this->manager->getRepository('AppBundle:Issue')->find($id);
            $issue->setState(self::STATE_IN_PROGRESS);
            $this->manager->flush();

            $queryBuilder = $this->manager->createQueryBuilder();

            $query = $queryBuilder
                ->update('AppBundle:Breaks', 'b')
                ->set('b.end', ':endTime')
                ->where('b.issueId = :issueId')
                ->andWhere($queryBuilder->expr()->isNull('b.end'))
                ->setParameter('issueId', $id)
                ->setParameter('endTime', $dateTime)
                ->getQuery()
            ;
            $result = $query->execute();

            if (!$result) {
                throw new \Doctrine\ORM\ORMException;
            }

            $this->manager->getConnection()->commit();

            return $issue;
        } catch (\Exception $ex) {

            $this->manager->getConnection()->rollBack();

            return null;
        }
    }

    /**
     * Set state Stop
     * 
     * @param int $id
     * @param string $description
     * @return Issue|null
     */
    public function stateIssueStop(int $id, string $description)
    {

        $dateTime = $this->getCurrentDateTIme();

        try {

            $issue = $this->manager->getRepository('AppBundle:Issue')->find($id);
            $totalTime = $this->getCalculatedTimeForIssue($issue);
            $issue->setState(self::STATE_END);
            $issue->setDescription($description);
            $issue->setEnd($dateTime);
            $issue->setTotalTime($totalTime);
            $this->manager->flush();

            return $issue;
        } catch (\Exception $ex) {

            return null;
        }
    }

    /**
     * 
     * Get total breaks time
     * 
     * @param int $issueId
     * @return type
     */
    public function getBreaksTimeTotal(int $issueId)
    {
        $sql = "SELECT sum(UNIX_TIMESTAMP(end)-UNIX_TIMESTAMP(begin)) as breaks_total_time FROM breaks WHERE issue_id = :issueId";

        $queryBuilder = $this->manager
            ->getConnection()
            ->prepare($sql)
        ;
        $queryBuilder->execute(array('issueId' => $issueId));

        $result = $queryBuilder->fetch();

        if (!empty($result['breaks_total_time'])) {
            return $result['breaks_total_time'];
        }

        return null;
    }

    /**
     * Calculate how long the issue lasted (excluding breaks time)
     * 
     * @param Issue $issue
     * @return type
     */
    public function getCalculatedTimeForIssue(Issue $issue)
    {
        $timeBegin = $issue->getBegin()->getTimestamp();
        $breaksTotalTime = $this->getBreaksTimeTotal($issue->getId());
        $time = time() - $timeBegin - $breaksTotalTime;
        return $time;
    }
}
