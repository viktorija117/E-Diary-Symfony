<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="direktor")
 */
class Direktor implements UserInterface
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
    private $ime;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $prezime;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $korisnicko_ime;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $lozinka;

    public function __construct($ime, $prezime, $korisnicko_ime, $lozinka) {
        $this->ime = $ime;
        $this->prezime = $prezime;
        $this->korisnicko_ime = $korisnicko_ime;
        $this->lozinka = $lozinka;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIme()
    {
        return $this->ime;
    }

    public function getPrezime()
    {
        return $this->prezime;
    }

    public function getKorisnickoIme(): ?string
    {
    return $this->korisnicko_ime;
    }

    public function getLozinka()
    {
        return $this->lozinka;
    }

    public function setIme($ime)
    {
        $this->ime = $ime;
    }

    public function setPrezime($prezime)
    {
        $this->prezime = $prezime;
    }

    public function setKorisnickoIme(string $korisnicko_ime): void
    {
    $this->korisnicko_ime = $korisnicko_ime;
    }


    public function setLozinka($lozinka)
    {
        $this->lozinka = $lozinka;
    }

    // Moramo imati ove funkcije (pa i prazne) jer su obavezne
    public function getRoles()
    {
        // Ovdje možete implementirati logiku za vraćanje uloga korisnika
        return ['ROLE_DIREKTOR'];
    }

    public function getPassword()
    {
        return $this->lozinka;
    }

    public function getSalt()
    {
        // Implementacija koja vraća so (ako koristite šifrovanje lozinke)
    }

    public function eraseCredentials()
    {
        // Implementacija koja briše osetljive podatke korisnika ukoliko je to potrebno
    }

    // Takodje obavezne funkcije koje zahteva UserInterface
    public function getUserIdentifier()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->korisnicko_ime;
    }
}