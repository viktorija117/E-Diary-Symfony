<?php
namespace App\Core;

use PDO;
use PDOException;

class Baza
{
    private static $instanca;
    private $konekcija;

    // konstruktor koji uspostavlja vezu sa bazom podataka koristeci PDO.
    private function __construct()
    {
        try {
            $this->konekcija = new PDO('mysql:host=127.0.0.1;dbname=bazaZaWebProjekatDnevnik', 'root', '');
            $this->konekcija->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // u slucaju greske pri povezivanju sa bazom podataka
            echo 'Greška pri povezivanju sa bazom podataka: ' . $e->getMessage();
            die();
        }
    }
    // funkcija koja vraca vezu sa bazom podataka
    public static function getInstance()
    {
        if (self::$instanca == null) {
            self::$instanca = new self();
        }
        return self::$instanca->getKonekcija();
    }

    public function getKonekcija()
    {
        return $this->konekcija;
    }
}
