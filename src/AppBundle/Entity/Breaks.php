<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Breaks
 *
 * @ORM\Table(name="breaks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BreaksRepository")
 */
class Breaks
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
     * @var int
     * @ORM\ManyToOne(targetEntity="App\Entity\Issue")
     * @ORM\Column(name="issue_id", type="integer")
     */
    private $issueId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="begin", type="datetime")
     */
    private $begin;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

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
     * Set issueId.
     *
     * @param int $issueId
     *
     * @return Breaks
     */
    public function setIssueId($issueId)
    {
        $this->issueId = $issueId;

        return $this;
    }

    /**
     * Get issueId.
     *
     * @return int
     */
    public function getIssueId()
    {
        return $this->issueId;
    }

    /**
     * Set begin.
     *
     * @param \DateTime $begin
     *
     * @return Breaks
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * Get begin.
     *
     * @return \DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Set end.
     *
     * @param \DateTime|null $end
     *
     * @return Breaks
     */
    public function setEnd($end = null)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end.
     *
     * @return \DateTime|null
     */
    public function getEnd()
    {
        return $this->end;
    }
}
