<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Word
 *
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WordRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity("word", message="You can't re-use an existing word.")
 * @UniqueEntity("date", message="There is already a word scheduled on that date.")
 */
class Word
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="4", minMessage="Let's use bigger words")
     */
    protected $word;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $pronunciation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Definition", mappedBy="word", cascade={"persist", "remove"})
     */
    private $definitions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * Word constructor
     */
    public function __construct()
    {
        $this->definitions = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * @param string $word
     *
     * @return self
     */
    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return string
     */
    public function getPronunciation()
    {
        return $this->pronunciation;
    }

    /**
     * @param string $pronunciation
     *
     * @return self
     */
    public function setPronunciation($pronunciation)
    {
        $this->pronunciation = $pronunciation;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param Definition $definition
     *
     * @return $this
     */
    public function addDefinition(Definition $definition)
    {
        if (!$this->getDefinitions()->contains($definition)) {
            $this->definitions->add($definition);
        }

        $definition->setWord($this);

        return $this;
    }

    /**
     * @param Definition $definition
     *
     * @return $this
     */
    public function removeDefinition(Definition $definition)
    {
        $this->definitions->removeElement($definition);

        $definition->setWord(null);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime());
        }

        $this->setUpdatedAt(new \DateTime());
    }
}
