<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OdjavljivanjeController extends AbstractController{

    private $tokenStorage;
    private $eventDispatcher;

    public function __construct(TokenStorageInterface $tokenStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**  
     * @Route("/odjavljivanje", name="app_odjavljivanje")  
    */
    public function odjavi(Request $request, AuthenticationUtils $authenticationUtils): Response{
        
        // trenutni token iz token podataka => aktivni korisnik
        $token = $this->tokenStorage->getToken();

        if ($token !== null) {
            // signalizujemo odjavljivanje
            $event = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch($event, 'security.interactive_logout');
            
            // oslobadjamo token iz token podataka
            $this->tokenStorage->setToken(null);
        }

        // uspesno smo se odjavili
        return $this->redirectToRoute('app_pocetna');
    }
}