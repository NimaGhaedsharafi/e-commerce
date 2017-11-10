<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Variant
 *
 * @ORM\Table(name="variants")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VariantRepository")
 */
class Variant extends BaseEntity
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
     *
     * @ORM\Column(name="product_id", type="integer")
     * @ORM\OneToMany(targetEntity="Product", mappedBy="variant")
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="color", type="integer")
     */
    private $color;


    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set color
     *
     * @param integer $color
     *
     * @return Variant
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return int
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return Variant
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Variant
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
}
