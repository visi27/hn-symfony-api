<?php
/**
 * Created by Evis Bregu <evis.bregu@gmail.com>.
 * Date: 5/11/18
 * Time: 11:25 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FavoriteRepository")
 * @ORM\Table(name="user_favorites")
 */
class Favorites
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    /**
     * @ORM\Column(type="string")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=30, nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $createdAtI;

    /**
     * @ORM\Column(type="integer")
     */
    private $numComments;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $objectID;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $storyText;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $url;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Favorites
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     *
     * @return Favorites
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return Favorites
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAtI()
    {
        return $this->createdAtI;
    }

    /**
     * @param mixed $createdAtI
     *
     * @return Favorites
     */
    public function setCreatedAtI($createdAtI)
    {
        $this->createdAtI = $createdAtI;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumComments()
    {
        return $this->numComments;
    }

    /**
     * @param mixed $numComments
     *
     * @return Favorites
     */
    public function setNumComments($numComments)
    {
        $this->numComments = $numComments;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getObjectID()
    {
        return $this->objectID;
    }

    /**
     * @param mixed $objectID
     *
     * @return Favorites
     */
    public function setObjectID($objectID)
    {
        $this->objectID = $objectID;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param mixed $points
     *
     * @return Favorites
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStoryText()
    {
        return $this->storyText;
    }

    /**
     * @param mixed $storyText
     *
     * @return Favorites
     */
    public function setStoryText($storyText)
    {
        $this->storyText = $storyText;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return Favorites
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return Favorites
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
