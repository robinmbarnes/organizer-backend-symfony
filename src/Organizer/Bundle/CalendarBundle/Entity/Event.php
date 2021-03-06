<?php


namespace Organizer\Bundle\CalendarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Todo
 *
 * @ORM\Table("event")
 * @ORM\Entity(repositoryClass="Organizer\Bundle\CalendarBundle\Repository\EventRepository")
 * @JMS\AccessType("public_method")
 */
class Event
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\ReadOnly
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     * @Assert\DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     * @Assert\DateTime
     */
    private $endDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_all_day", type="boolean")
     * @Assert\Type(type="bool")
     */
    private $isAllDay;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Event
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Event
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Event
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set isAllDay
     *
     * @param boolean $isAllDay
     * @return Event
     */
    public function setIsAllDay($isAllDay)
    {
        $this->isAllDay = $isAllDay;

        return $this;
    }

    /**
     * Get isAllDay
     *
     * @return boolean 
     */
    public function getIsAllDay()
    {
        return $this->isAllDay;
    }

    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if($this->getStartDate()->getTimestamp() > $this->getEndDate()->getTimestamp()) {
            $context->buildViolation('The start date cannot be less than the end date')
                ->atPath('startDate')
                ->addViolation();
            $context->buildViolation('The start date cannot be less than the end date')
                ->atPath('endDate')
                ->addViolation();
        }
    }
}
