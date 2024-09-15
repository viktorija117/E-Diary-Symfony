<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="student_slusa_predmete")
 */
class Ocena
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="id")
     */
    private $student_id;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Predmet")
     * @ORM\JoinColumn(name="predmet_id", referencedColumnName="id")
     */
    private $predmet_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ocena;

    public function __construct($predmet_id, $student_id, $ocena) {
        $this->predmet_id = $predmet_id;
        $this->student_id = $student_id;
        $this->ocena = $ocena;
    }

    public function getStudent()
    {
        return $this->student_id;
    }

    public function getPredmet()
    {
        return $this->predmet_id;
    }

    public function getOcena()
    {
        return $this->ocena;
    }

    public function setOcena($ocena)
    {
        $this->ocena = $ocena;
    }
}
