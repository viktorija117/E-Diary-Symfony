<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ocena;
use App\Entity\Profesor;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;


// Za excel i pdf su nam potrebne biblioteke koje uvodimo komandom
//composer require phpoffice/phpspreadsheet symfony/http-foundation symfony/mime


class LogController extends AbstractController
{

    // Moramo uvesti entityManager-a jer nam treba veza sa bazom podataka
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/preuzmiOcene", name="app_preuzmi_ocene")
     */
    public function preuzmiOcene(): BinaryFileResponse
    {
        // Uzimamo sve ocene iz tabele student_slusa_predmete
        $ocene = $this->entityManager->getRepository(Ocena::class)->findAll();

        // Podesavamo sta ce pisati u excel fajlu
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Student');
        $sheet->setCellValue('B1', 'Predmet');
        $sheet->setCellValue('C1', 'Ocena');

        // Prvi red su podaci koje smo uneli iznad i krecemo od drugog reda
        $row = 2;
        foreach ($ocene as $ocena) {
            $sheet->setCellValue('A' . $row, $ocena->getStudent()->getIme() . ' ' . $ocena->getStudent()->getPrezime());
            $sheet->setCellValue('B' . $row, $ocena->getPredmet()->getNazivPredmeta());
            $sheet->setCellValue('C' . $row, $ocena->getOcena());
            $row++;
        }

        // Upisujemo u excel fajl
        $writer = new Xlsx($spreadsheet);
        $tempFilePath = sys_get_temp_dir() . '/ocene.xlsx';
        $writer->save($tempFilePath);

        // Tip datoteke na osnovu ekstenzije
        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($tempFilePath);

        // Preuzimamo excel fajl
        $response = new BinaryFileResponse($tempFilePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ocene.xlsx'
        );
        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    /**
     * @Route("/preuzmiProfesore", name="app_preuzmi_profesore")
     */
    public function preuzmiProfesore(): Response
    {
        // Dohvatimo podatke o profesorima
        $profesori = $this->getDoctrine()->getRepository(Profesor::class)->findAll();

        // Renderovanje stranice sa podacima
        $template = $this->renderView('PregledajProfesore.html.twig', ['profesori' => $profesori]);

        // Pravimo ovi dokument i podesavamo osnovne informacije za njega
        $dompdf = new Dompdf();
        $dompdf->loadHtml($template);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Preuzimamo pdf fajl
        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="pregled_profesora.pdf"');

        return $response;
    }
}
