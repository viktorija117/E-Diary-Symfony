<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Direktor;
use App\Entity\Profesor;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LogovanjeController extends AbstractController
{
    private $entityManager;
    private $eventDispatcher;
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/pokusajLogovanja", name="app_logovanje_pokusaj")
     */
    public function pokusajLogovanja(Request $request): Response
    {
        // Uzimamo korisnicko ime i lozinku iz forme 
        $korisnicko_ime = $request->get('korisnicko_ime');
        $lozinka = $request->get('lozinka');

        // Proveravamo da li su obe vrednosti unete
        if (empty($korisnicko_ime) || empty($lozinka)) {
            return $this->render('Pocetna.html.twig', ['error' => 'Niste popunili sva polja!']);
        }

        $direktor = null; 
        $profesor = null; 
        $student = null; 

        $direktor = $this->entityManager->getRepository(Direktor::class)->findOneBy([
            'korisnicko_ime' => $korisnicko_ime
        ]);
        // Trazimo ko se tacno ulogovao da bismo znali gde da proveravamo podatke
        if (!$direktor || (md5($lozinka) !== $direktor->getLozinka())) {
            $profesor = $this->entityManager->getRepository(Profesor::class)->findOneBy([
                'korisnicko_ime' => $korisnicko_ime
            ]);
            if (!$profesor  || (md5($lozinka) !== $profesor->getLozinka())){
                $student = $this->entityManager->getRepository(Student::class)->findOneBy([
                    'korisnicko_ime' => $korisnicko_ime
                ]);
                if (!$student || (md5($lozinka) !== $student->getLozinka())){
                    return $this->render('Pocetna.html.twig', ['error' => 'Proverite korisnicko ime i lozinku!']);
                }
            }
        }

        // U zavisnosti od toga ko je ulogovan podesavamo token
        if($direktor){
            $direktor = $this->entityManager->getRepository(Direktor::class)->findOneBy([
                'korisnicko_ime' => $korisnicko_ime
            ]);
            $token = new UsernamePasswordToken($direktor, null, 'main', $direktor->getRoles());
            $this->tokenStorage->setToken($token);
        }
        if($profesor){
            $profesor = $this->entityManager->getRepository(Profesor::class)->findOneBy([
                'korisnicko_ime' => $korisnicko_ime
            ]);
            $token = new UsernamePasswordToken($profesor, null, 'main', $profesor->getRoles());
            $this->tokenStorage->setToken($token);
        }
        if($student){
            $student = $this->entityManager->getRepository(Student::class)->findOneBy([
                'korisnicko_ime' => $korisnicko_ime
            ]);
            $token = new UsernamePasswordToken($student, null, 'main', $student->getRoles());
            $this->tokenStorage->setToken($token);
        }

        $dogadjaj = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch($dogadjaj, 'security.interactive_login');

        // Trazimo ko se tacno ulogovao da bismo znali na koju stranicu da ga odvedemo
        if ($direktor) {
            return $this->render('Direktor.html.twig');
        } elseif ($profesor) {
            return $this->render('Profesor.html.twig');
        } elseif ($student) {
            return $this->render('Student.html.twig');
        }
        return $this->render('Pocetna.html.twig');
    }

    /**
     * @Route("/logovanje", name="app_pocetna")
     */
    public function loginStranica(): Response
    {
        return $this->render('Pocetna.html.twig');
    }
}
