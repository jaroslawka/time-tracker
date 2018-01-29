<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Issue
 *
 * @ORM\Table(name="issue")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IssueRepository")
 */
class Issue
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var state
     *
     * @ORM\Column(name="state", type="smallint")
     */
    private $state;

    /**
     * @var string|null
     *
     * @ORM\Column(name="desciption", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var datetime
     *
     * @ORM\Column(name="begin", type="datetime")
     */
    private $begin;

    /**
     * @var datetime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var totaltime
     *
     * @ORM\Column(name="totaltime", type="integer", nullable=true)
     */
    private $totaltime;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
        return $this->state;
    }

    /**
     * Set totaltime.
     *
     * @param int $totaltime
     *
     * @return Issue
     */
    public function setTotalTime($totaltime)
    {
        $this->totaltime = $totaltime;

        return $this;
    }

    /**
     * Get totaltime.
     *
     * @return int
     */
    public function getTotalTime()
    {
        return $this->totaltime;
    }

    /**
     * Set desciption.
     *
     * @param string|null $desciption
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
        return $this->description;
    }

    /**
     * Set begin.
     *
     * @param datetime $begin
     *
     * @return Issue
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin.
     *
     * @return datetime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end.
     *
     * @param datetime $end
     *
     * @return Issue
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end.
     *
     * @return datetime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return Issue
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
