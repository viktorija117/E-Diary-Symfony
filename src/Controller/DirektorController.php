<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profesor;
use App\Entity\Predmet;
use App\Entity\Student;
use App\Entity\Ocena;
use Psr\Log\LoggerInterface;


class DirektorController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/klikPregledajProfesore", name="app_pregledaj_klik")
     */
    public function klikPregledaj()
    {
        $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();

        return $this->render('PregledajProfesore.html.twig', [
            'profesori' => $profesori,
        ]);
    }
    /**
     * @Route("/klikPregledajStudenteDirektor", name="app_pregledaj_studente_direktor_klik")
     */
    public function klikPregledajStudenteDirektor()
    {
        $studenti = $this->entityManager->getRepository(Student::class)->findAll();

        return $this->render('PregledajStudenteDirektor.html.twig', [
            'studenti' => $studenti,
        ]);
    }
    /**
     * @Route("/klikPregledajStudenteProfesor", name="app_pregledaj_studente_profesor_klik")
     */
    public function klikPregledajProfesor()
    {
        $ulogovaniProfesor = $this->getUser();
        if ($ulogovaniProfesor instanceof Profesor){
        $predmet = $ulogovaniProfesor->getPredmetId();
        $studenti = $this->entityManager->getRepository(Student::class)->findAll();
        $ocene = [];
        foreach ($studenti as $student) {
            $trenutnaOcena = $this->entityManager->getRepository(Ocena::class)->findOneBy(['predmet_id'=> $predmet, 'student_id'=> $student->getId()]);
            array_push($ocene, $trenutnaOcena);
        }

        return $this->render('PregledajStudenteProfesor.html.twig', [
            'studenti' => $studenti,
            'ocene' => $ocene,
        ]);}
    }

    /**
     * @Route("/klikPregledajStudentoveOcene", name="app_pregledaj_studentove_ocene_klik")
     */
    public function klikPregledajStudentoveOcene()
    {
        $student = $this->getUser(); // Dobijamo trenutno ulogovanog studenta
        if ($student instanceof Student){
            $ocene = $this->getDoctrine()->getRepository(Ocena::class)->findBy(['student_id' => $student->getId()]);

            return $this->render('StudentoveOcene.html.twig', [
                'ocene' => $ocene,
            ]);
        }
    }

    /**
     * @Route("/dodajProfesora", name="dodaj_profesora", methods={"POST"})
     */
    public function dodajProfesora(Request $request): Response
    {
        $ime = $request->request->get('ime');
        $prezime = $request->request->get('prezime');
        $korisnicko_ime = $request->request->get('korisnicko_ime');
        $lozinka = $request->request->get('lozinka');
        $predmet_id = $request->request->get('predmet_id');

        // Provera da li su sva polja uneta
        if (!$ime || !$prezime || !$korisnicko_ime || !$lozinka) {
            $error = 'Sva polja su obavezna.';
            $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();

            return $this->render('PregledajProfesore.html.twig', [
                'error' => $error,
                'profesori' => $profesori,
            ]);
        }

        // Provera da li već postoji korisnik sa istim korisničkim imenom ili lozinkom
        $postojeciProfesor = $this->entityManager->getRepository(Profesor::class)->findOneBy(['korisnicko_ime' => $korisnicko_ime]);
        if ($postojeciProfesor) {
            $error = 'Profesor sa istim korisničkim imenom već postoji.';
            $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();

            return $this->render('PregledajProfesore.html.twig', [
                'error' => $error,
                'profesori' => $profesori,
            ]);
        }

        // Pronalaženje instance objekta Predmet na osnovu predmet_id
        $predmet = $this->entityManager->find(Predmet::class, $predmet_id);

        if (!$predmet) {
            $error = 'Predmet sa datim ID-om ne postoji.';
            $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();

            return $this->render('PregledajProfesore.html.twig', [
                'error' => $error,
                'profesori' => $profesori,
            ]);
        }

        $noviProfesor = new Profesor($ime, $prezime, $korisnicko_ime, md5($lozinka), $predmet);

        $this->entityManager->persist($noviProfesor);
        $this->entityManager->flush();

        $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();

        return $this->render('PregledajProfesore.html.twig', [
            'profesori' => $profesori,
        ]);
    }

    /**
     * @Route("/otpustiProfesora/{id}", name="otpusti_profesora", methods={"GET"})
     */
    public function otpustiProfesora($id): Response
    {
        $profesor = $this->entityManager->getRepository(Profesor::class)->find($id);

        if (!$profesor) {
            $error = 'Profesor sa datim ID-om ne postoji.';
            $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();

            return $this->render('PregledajProfesore.html.twig', [
                'error' => $error,
                'profesori' => $profesori,
            ]);
        }

        // Provera da li postoje drugi profesori koji predaju isti predmet
        $predmet = $profesor->getPredmetId();
        $profesoriSaIstimPredmetom = $this->entityManager->getRepository(Profesor::class)->findBy(['predmet_id' => $predmet]);

        if (count($profesoriSaIstimPredmetom) <= 1) {
            $error = 'Nije moguće otpustiti profesora jer nema zamene.';
            $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();

            return $this->render('PregledajProfesore.html.twig', [
                'error' => $error,
                'profesori' => $profesori,
            ]);
        }

        $this->entityManager->remove($profesor);
        $this->entityManager->flush();

        $profesori = $this->entityManager->getRepository(Profesor::class)->findAll();
        return $this->render('PregledajProfesore.html.twig', [
            'profesori' => $profesori,
        ]);
    }

    /**
     * @Route("/dodajStudenta", name="dodaj_studenta", methods={"POST"})
     */
    public function dodajStudenta(Request $request): Response
    {
        $ime = $request->request->get('ime');
        $prezime = $request->request->get('prezime');
        $korisnicko_ime = $request->request->get('korisnicko_ime');
        $lozinka = $request->request->get('lozinka');

        // Provera da li su sva polja uneta
        if (!$ime || !$prezime || !$korisnicko_ime || !$lozinka) {
            $error = 'Sva polja su obavezna.';
            $studenti = $this->entityManager->getRepository(Student::class)->findAll();

            return $this->render('PregledajStudenteDirektor.html.twig', [
                'error' => $error,
                'studenti' => $studenti,
            ]);
        }

        // Provera da li već postoji korisnik sa istim korisničkim imenom ili lozinkom
        $postojeciStudent = $this->entityManager->getRepository(Student::class)->findOneBy(['korisnicko_ime' => $korisnicko_ime]);
        if ($postojeciStudent) {
            $error = 'Student sa istim korisničkim imenom već postoji.';
            $studenti = $this->entityManager->getRepository(Student::class)->findAll();

            return $this->render('PregledajStudenteDirektor.html.twig', [
                'error' => $error,
                'studenti' => $studenti,
            ]);
        }

        $noviStudent = new Student($ime, $prezime, $korisnicko_ime, md5($lozinka));

        $this->entityManager->persist($noviStudent);
        $this->entityManager->flush();

        $predmeti = $this->entityManager->getRepository(Predmet::class)->findAll();

        foreach ($predmeti as $predmet) {
            $sql = "INSERT INTO student_slusa_predmete (student_id, predmet_id, ocena) VALUES (:studentId, :predmetId, :ocena)";
            $stmt = $this->entityManager->getConnection()->prepare($sql);
            $stmt->bindValue('studentId', $noviStudent->getId());
            $stmt->bindValue('predmetId', $predmet->getId());
            $stmt->bindValue('ocena', 5);
            $stmt->execute();
        }

        $studenti = $this->entityManager->getRepository(Student::class)->findAll();
        return $this->render('PregledajStudenteDirektor.html.twig', [
            'studenti' => $studenti,
        ]);
    }

    /**
     * @Route("/izbaciStudenta/{id}", name="izbaci_studenta", methods={"GET"})
     */
    public function izbaciStudenta($id): Response
    {
        $student = $this->entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            $error = 'Student sa datim ID-om ne postoji.';
            $studenti = $this->entityManager->getRepository(Student::class)->findAll();

            return $this->render('PregledajStudenteDirektor.html.twig', [
                'error' => $error,
                'studenti' => $studenti,
            ]);
        }

        $sql = 'DELETE FROM student_slusa_predmete WHERE student_id = :studentId';

        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('studentId', $student->getId());
        $query->execute();

        $this->entityManager->remove($student);
        $this->entityManager->flush();

        $studenti = $this->entityManager->getRepository(Student::class)->findAll();
        return $this->render('PregledajStudenteDirektor.html.twig', [
            'studenti' => $studenti,
        ]);
    }
  
    /**
     * @Route("/upisiOcenu", name="upisi_ocenu", methods={"POST"})
     */
    public function upisiOcenu(Request $request): Response
    {
        $ulogovaniProfesor = $this->getUser();
        if ($ulogovaniProfesor instanceof Profesor){
            $korisnickoIme = $request->request->get('korisnicko_ime');
            $ocena = $request->request->get('ocena');
            $ocene = [];

        // Provera da li su sva polja uneta
        if (!$korisnickoIme || !$ocena) {
            $error = 'Sva polja su obavezna.';
            $studenti = $this->entityManager->getRepository(Student::class)->findAll();

            return $this->render('PregledajStudenteProfesor.html.twig', [
                'error' => $error,
                'studenti' => $studenti,
                'ocene' => $ocene,
            ]);
        }

        $student = $this->entityManager->getRepository(Student::class)->findOneBy(['korisnicko_ime' => $korisnickoIme]);

        if (!$student) {
            $error = 'Student sa datim korisničkim imenom ne postoji.';
            $studenti = $this->entityManager->getRepository(Student::class)->findAll();

            return $this->render('PregledajStudenteProfesor.html.twig', [
                'error' => $error,
                'studenti' => $studenti,
                'ocene' => $ocene,
            ]);
        }

        $predmet = $ulogovaniProfesor->getPredmetId(); // Podešavanje predmeta koji predaje ulogovani profesor

        $sql = "UPDATE student_slusa_predmete SET ocena = :ocena WHERE student_id = :studentId AND predmet_id = :predmetId";
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->bindValue('ocena', $ocena);
        $stmt->bindValue('studentId', $student->getId());
        $stmt->bindValue('predmetId', $predmet->getId());
        $stmt->execute();
        $studenti = $this->entityManager->getRepository(Student::class)->findAll();

        foreach ($studenti as $student) {
            $trenutnaOcena = $this->entityManager->getRepository(Ocena::class)->findOneBy(['predmet_id'=> $predmet, 'student_id'=> $student->getId()]);
            array_push($ocene, $trenutnaOcena);
        }

        return $this->render('PregledajStudenteProfesor.html.twig', [
            'studenti' => $studenti,
            'ocene' => $ocene,
        ]);
        }
    }
}



