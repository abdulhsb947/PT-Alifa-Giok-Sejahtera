<?php

namespace App\Services;

use App\Models\Agreement;
use setasign\Fpdi\Fpdi;

class GenerateSignedPdfService
{
    /**
     * Generate PDF final dengan tanda tangan customer
     */
    public function generate(Agreement $agreement)
    {
        $pdf = new Fpdi('P', 'pt');

        // ============================
        // Lokasi file
        // ============================

        $fullPdfPath = storage_path(
            'app/public/' . $agreement->file
        );


        $fullSignaturePath = storage_path(
            'app/public/' . $agreement->signature_file
        );



        // ============================
        // Validasi file
        // ============================

        if (!file_exists($fullPdfPath)) {
            throw new \Exception(
                'File PDF tidak ditemukan : ' . $fullPdfPath
            );
        }

        if (!file_exists($fullSignaturePath)) {
            throw new \Exception(
                'File tanda tangan tidak ditemukan : ' . $fullSignaturePath
            );
        }

        // ============================
        // Validasi posisi tanda tangan
        // ============================

        if (
            is_null($agreement->signature_x) ||
            is_null($agreement->signature_y) ||
            empty($agreement->signature_width) ||
            empty($agreement->signature_height)
        ) {

            throw new \Exception(
                'Posisi tanda tangan belum ditentukan.'
            );
        }

        // ============================
        // Buka PDF
        // ============================

        $pageCount = $pdf->setSourceFile(
            $fullPdfPath
        );


        if (
            $agreement->signature_page < 1 ||
            $agreement->signature_page > $pageCount
        ) {
            throw new \Exception(
                'Halaman tanda tangan tidak valid.'
            );
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            $template = $pdf->importPage($pageNo);

            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage(
                $size['orientation'],
                [$size['width'], $size['height']]
            );

            $pdf->useTemplate(
                $template,
                0,
                0,
                $size['width'],
                $size['height'],
                true
            );

            if ($pageNo == $agreement->signature_page) {

            

                $pdf->Image(
                    $fullSignaturePath,
                    $agreement->signature_x,
                    $agreement->signature_y,
                    $agreement->signature_width,
                    $agreement->signature_height
                );
            }
        }

        // ============================
        // Folder output
        // ============================

        $directory = storage_path(
            'app/public/agreements/final'
        );

        if (!file_exists($directory)) {

            mkdir(
                $directory,
                0777,
                true
            );
        }

        // ============================
        // Nama file
        // ============================

        $finalPath =
            'agreements/final/agreement_' .
            $agreement->order_id .
            '_v' .
            $agreement->version .
            '_' .
            time() .
            '.pdf';

        $savePath = storage_path(
            'app/public/' . $finalPath
        );

        // ============================
        // Simpan PDF
        // ============================

        $pdf->Output(
            $savePath,
            'F'
        );

        if (!file_exists($savePath)) {

            throw new \Exception(
                'PDF final gagal dibuat.'
            );
        }

        // ============================
        // Simpan lokasi PDF Final
        // ============================

        $agreement->update([

            'final_file' => $finalPath

        ]);

        return $finalPath;
    }
}
