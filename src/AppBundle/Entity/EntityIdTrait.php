<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait to use for entities with UUID.
 */
trait EntityIdTrait
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid", nullable=false)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }
}