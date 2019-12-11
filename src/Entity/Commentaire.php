<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="Commentaire", indexes={@ORM\Index(name="fk_user_comment", columns={"user"}), @ORM\Index(name="fk_publication", columns={"publications"})})
 * @ORM\Entity(repositoryClass="App\Repository\CommentairesRepository")
 */
class Commentaire
{
    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=false)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \App\Entity\Publications
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Publications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publications", referencedColumnName="id")
     * })
     */
    private $publications;

    /**
     * @var \App\Entity\Utilisateurs
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateurs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Commentaire
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commentaire
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
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
     * Set publications
     *
     * @param \App\Entity\Publications $publications
     *
     * @return Commentaire
     */
    public function setPublications(\App\Entity\Publications $publications = null)
    {
        $this->publications = $publications;

        return $this;
    }

    /**
     * Get publications
     *
     * @return \App\Entity\Publications
     */
    public function getPublications()
    {
        return $this->publications;
    }

    /**
     * Set user
     *
     * @param \App\Entity\Utilisateurs $user
     *
     * @return Commentaire
     */
    public function setUser(\App\Entity\Utilisateurs $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\Utilisateurs
     */
    public function getUser()
    {
        return $this->user;
    }
}
