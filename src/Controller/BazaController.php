<?php

namespace App\Controller;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
// ovo u projekat ubacujemo preko terminala (composer require doctrine/dbal)
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BazaController extends AbstractController
{
    // @Route je anotacija koja oznacava definisanje rute, tj. definisanje putanje na kojoj mozemo naci datu stranicu
    /**
     * @Route("/baza", name="app_baza")
     */
    public function dodaj(): Response
    {
        // pravimo doctrine DBAL konfiguraciju
        $konfig = new Configuration();

        //informacije za povezivanje sa bazom podataka
        $parametriKonekcije = [ 'url' => 'mysql://root:@localhost:3306',  ];

        // pravimo objekat konekcije
        $konekcija = DriverManager::getConnection($parametriKonekcije, $konfig);

        // pamtimo putanju do SQL fajla koji kreira nasu bazu
        $sqlPutanja = dirname(dirname(__DIR__)) . '/kreirajBazu.sql'; // 

        // citamo sadrzaj SQL fajla
        $sql = file_get_contents($sqlPutanja);


        // izvrsavamo SQL fajl
        $konekcija->query($sql);

        return new Response('Kreiranje baze uspesno!');
    }
}
