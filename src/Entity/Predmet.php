<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="predmet")
 */
class Predmet
{
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=45)
     */
    private $naziv_predmeta;

    public function __construct($naziv_predmeta) {
        $this->naziv_predmeta = $naziv_predmeta;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNazivPredmeta()
    {
        return $this->naziv_predmeta;
    }

    public function setNazivPredmeta($naziv_predmeta)
    {
        $this->naziv_predmeta = $naziv_predmeta;
    }
}
