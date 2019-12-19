<?php

namespace App\Controller;

use App\Entity\ExportDonnees;
use App\Entity\MembreFamille;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExportDonneesController extends AbstractController
{
    /**
     * @Route("/export/updates", name="ExportDonnees.exportUpdates")
     */
    public function exportUpdates()
    {
        // get all updates
        $updates = $this->getUpdates();
        dump($updates);
        // generate excel doc
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // set table head
        $sheet->setCellValue('A1', 'Code client');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prénom');
        $sheet->setCellValue('D1', 'Adresse');
        $sheet->setCellValue('E1', 'N° telephone');
        $sheet->setCellValue('F1', 'N° fixe');
        $sheet->setCellValue('G1', 'E-mail');
        // loop
        for ($i = 0; $i < sizeof($updates); $i ++) {
            $m = $updates[$i];
            $sheet->setCellValue('A'.($i + 2), $m->getNoClient());
            $sheet->setCellValue('B'.($i + 2), $m->getNom());
            $sheet->setCellValue('C'.($i + 2), $m->getPrenom());
            $sheet->setCellValue('D'.($i + 2), $m->getRepresentantFamille()->getAdresse());
            $sheet->setCellValue('E'.($i + 2), $m->getRepresentantFamille()->getNoMobile());
            $sheet->setCellValue('F'.($i + 2), $m->getRepresentantFamille()->getNoFixe());
            if ($m->getCategorie() == 'Majeur') {
                if ($m->getInformationMajeur() != null) {
                    $email = $m->getInformationMajeur()->getMail();
                }
                else {
                    $email = "non renseignée";
                }
            }
            else {
                $email = 'pas d\'email personnelle';
            }
            $sheet->setCellValue('G'.($i + 2), $email);
        }
        // save file
        $writer = new Xlsx($spreadsheet);
        $date = date_format(new \DateTime(), 'Y-m-d_H:i:s');
        $writer->save($date. '.xlsx');
        // update export table
        $this->updateExport($updates);
        return $this->redirectToRoute('Gestionnaire.showListeFamilles');
    }

    public function getUpdates()
    {
        $updates = [];
        // get all membre
        $membres = $this->getDoctrine()->getRepository(MembreFamille::class)->findBy(
            [],
            ['noClient' => 'ASC']
        );
        // last export date
        $exports = $this->getDoctrine()->getRepository(ExportDonnees::class)->findBy(
            [],
            ['dateDernierExport' => 'DESC']
        );
        $lastExportDate = ($exports == null) ? null : $exports[0]->getDateDernierExport();
        dump($lastExportDate);
        // loop membre
        for ($i = 0; $i < sizeof($membres); $i ++) {
            $m = $membres[$i];
            // last MAJ membre
            $lastMAJDate = $m->getDateMAJ();
            // check if update
            dump($lastMAJDate);
            if ($lastExportDate == null || $lastMAJDate > $lastExportDate) {
                array_push($updates, $m);
            }
        }
        return $updates;
    }

    public function updateExport($membres)
    {
        // new export
        $export = new ExportDonnees();
        $export->setDateDernierExport(new \DateTime());
        $this->getDoctrine()->getManager()->persist($export);
        $this->getDoctrine()->getManager()->flush();
        // link to membres
        foreach ($membres as $m) {
            $m->addExportDonnee($export);
            $this->getDoctrine()->getManager()->persist($m);
        }
    }
}
