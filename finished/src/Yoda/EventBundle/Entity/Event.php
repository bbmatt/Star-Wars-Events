<?php

namespace Yoda\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Yoda\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Yoda\EventBundle\Entity\Event
 *
 * @ORM\Table(name="yoda_event")
 * @ORM\Entity(repositoryClass="Yoda\EventBundle\Entity\EventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Event
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var datetime $time
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var string $location
     *
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var text $details
     *
     * @ORM\Column(name="details", type="text", nullable=true)
     */
    private $details;

    /**
     * @ORM\ManyToOne(targetEntity="Yoda\UserBundle\Entity\User", cascade={"delete"}, inversedBy="events")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(length=256, unique=true)
     */
    protected $slug;

    /**
     * @var datetime $created
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * @ORM\ManyToMany(targetEntity="Yoda\UserBundle\Entity\User")
     * @ORM\JoinTable(
     *      joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     *      )
     **/
    protected $attendees;

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set time
     *
     * @param datetime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * Get time
     *
     * @return datetime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set location
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set details
     *
     * @param text $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * Get details
     *
     * @return text 
     */
    public function getDetails()
    {
        return $this->details;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return \Yoda\EventBundle\Entity\datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \Yoda\EventBundle\Entity\datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \Yoda\EventBundle\Entity\datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \Yoda\EventBundle\Entity\datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    public function getAttendees()
    {
        return $this->attendees;
    }

    /**
     * @param \Yoda\UserBundle\Entity\User $user
     * @return bool
     */
    public function hasAttendee(User $user)
    {
        return $this->attendees->contains($user);
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->created) {
            $this->setCreated(new \DateTime());
        }
    }
}