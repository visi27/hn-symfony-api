<?php

/*
 *
 * (c) Evis Bregu <evis.bregu@gmail.com>
 *
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

// Instead of Hateoas we can still use our Links Annotation serializer subscriber to generate links
// * @Link(
// *  "self",
// *  route = "api_show_genus",
// *  params = { "id": "object.getId()" }
// * )

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlogPostRepository")
 * @ORM\Table(name="blog_posts")
 *
 * @Serializer\ExclusionPolicy("none")
 *
 * @Hateoas\Relation(
 *     "self",
 *     href=@Hateoas\Route(
 *          "api_v1.0_show_blog_post",
 *          parameters={"id"= "expr(object.getId())"}
 *     )
 * )
 * @Hateoas\Relation(
 *     "category",
 *     href=@Hateoas\Route(
 *          "api_v1.0_show_category",
 *          parameters={"id"= "expr(object.getCategory())"}
 *     ),
 *     embedded = "expr(object.getCategory())"
 * )
 *
 *
 * @SWG\Definition(
 *     definition="BlogPost",
 *     required={"title", "category", "summary", "content", "publishedAt"},
 *     properties={"id", "title", "category", "summary", "content", "publishedAt"},
 *     type="object",
 *     @SWG\Xml(name="BlogPost"))
 *
 */
class BlogPost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @Serializer\Exclude()
     *
     * @SWG\Property(format="int64")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Please enter a valid title")
     * @ORM\Column(type="string")
     *
     *  @SWG\Property()
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Serializer\Groups({"deep"})
     * @Assert\Valid()
     *
     * @SWG\Property()
     */
    private $category;

    /**
     * @Assert\NotBlank(message="Summary should not be empty")
     * @ORM\Column(type="text")
     *
     * @SWG\Property()
     */
    private $summary;

    /**
     * @Assert\NotBlank(message="Content should not be empty")
     * @ORM\Column(type="text")
     *
     * @SWG\Property()
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     *
     * @SWG\Property()
     */
    private $isPublished = true;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date")
     * @Type("DateTime<'Y-m-d'>")
     *
     * @SWG\Property()
     */
    private $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Serializer\Exclude()
     */
    private $user;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Used only in serialisaztion.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("category")
     */
    public function getCategoryId()
    {
        return $this->getCategory()->getId();
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getisPublished()
    {
        return $this->isPublished;
    }

    /**
     * @param mixed $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @param mixed $publishedAt
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
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
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Used only in serialisaztion.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("user")
     */
    public function getUserName()
    {
        return $this->getUser()->getUsername();
    }

    public function __toString()
    {
        return $this->title.' | '.$this->id;
    }
}
