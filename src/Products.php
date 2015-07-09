<?php

// src/Products.php

/**
 * @Entity @Table(name="products")
 **/
class Product
{
    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;

    /** @Column(type="string") * */
    protected $name;

    /** @Column(type="string", unique=true) * */
    protected $slug;

    /** @Column(type="text") * */
    protected $description;

    /** @Column(type="integer") * */
    protected $price;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        global $entityManager;
        $slug   = functions\Helpers::slugify($slug);
        $unique = false;
        $count  = 0;
        while ($unique == false) {
            $pageRepository = $entityManager->getRepository('Product');
            $has_slug       = $pageRepository->findOneBy(array('slug' => $slug));
            if (!$has_slug || ($has_slug->id == $this->id)) {
                $unique = true;
            } else {
                $count++;
                $slug = preg_replace('/(-\d$)/', '', $slug);
                $slug = $slug.'-'.$count;
            }
        }
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

}