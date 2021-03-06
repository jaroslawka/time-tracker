<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Helper\TimeHelper;
use AppBundle\Entity\Issue;
use AppBundle\Entity\Breaks;
use AppBundle\Model\IssueState;

/**
 * IssueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IssueRepository extends EntityRepository
{

    use TimeHelper;

    /**
     * Get Issue in progress
     * @return object
     */
    public function getIssueInProgressOrPaused($userId)
    {

        $manager = $this->getEntityManager();

        try {
            $queryBuilder = $manager->getRepository('AppBundle:Issue')->createQueryBuilder('e');
            $result = $queryBuilder
                ->where('e.userId = :userId ')
                ->andWhere('e.state != :state')
                ->setParameter('userId', $userId)
                ->setParameter('state', IssueState::STATE_END)
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
        $manager = $this->getEntityManager();

        $dateTime = $this->getCurrentDateTIme();

        try {
            $issue = new Issue();
            $issue->setState(IssueState::STATE_IN_PROGRESS);
            $issue->setUserId($userId);
            $issue->setBegin($dateTime);
            $issue->setDescription($description);
            $manager->persist($issue);
            $manager->flush();

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
        $manager = $this->getEntityManager();

        $dateTime = $this->getCurrentDateTIme();

        try {
            $manager->getConnection()->beginTransaction();

            $issue = $manager->getRepository('AppBundle:Issue')->find($id);
            $issue->setState(IssueState::STATE_PAUSED);
            $manager->flush();

            $break = new Breaks();
            $break->setIssueId($issue->getId());
            $break->setBegin($dateTime);
            $manager->persist($break);
            $manager->flush();

            $manager->getConnection()->commit();

            return $issue;
        } catch (\Exception $ex) {

            $manager->getConnection()->rollBack();

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

        $manager = $this->getEntityManager();

        $dateTime = $this->getCurrentDateTIme();

        try {

            $manager->getConnection()->beginTransaction();

            $issue = $manager->getRepository('AppBundle:Issue')->find($id);
            $issue->setState(IssueState::STATE_IN_PROGRESS);
            $manager->flush();

            $queryBuilder = $manager->createQueryBuilder();

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

            $manager->getConnection()->commit();

            return $issue;
        } catch (\Exception $ex) {

            $manager->getConnection()->rollBack();

            return null;
        }
    }

    /**
     * Set state Stop
     * 
     * @param int $id
     * @param string $description
     * @parat int $totalTime
     * @return Issue|null
     */
    public function stateIssueStop(int $id, string $description, int $totalTime)
    {
        $manager = $this->getEntityManager();

        $dateTime = $this->getCurrentDateTIme();

        try {

            $issue = $manager->getRepository('AppBundle:Issue')->find($id);
            $issue->setState(IssueState::STATE_END);
            $issue->setDescription($description);
            $issue->setEnd($dateTime);
            $issue->setTotalTime($totalTime);
            $manager->flush();

            return $issue;
        } catch (\Exception $ex) {

            return null;
        }
    }
}
