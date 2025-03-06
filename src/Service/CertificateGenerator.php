<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;

class CertificateGenerator
{
    public function generateCertificate(string $userName, string $courseName): Response
    {
        // Configure Dompdf options
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Generate HTML for the certificate
        $html = $this->renderCertificateHtml($userName, $courseName);

        // Load HTML into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the PDF
        $dompdf->render();

        // Stream the PDF to the browser
        $output = $dompdf->output();
        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="certificate.pdf"');

        return $response;
    }

    private function renderCertificateHtml(string $userName, string $courseName): string
    {
        return "
       <!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Certificate of Completion</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f7fb;
        }
        .certificate-container {
            width: 210mm;
            height: 297mm;
            max-width: 100%;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30mm;
            text-align: center;
            box-sizing: border-box;
        }
        .certificate-header {
            font-size: 36px;
            font-weight: bold;
            color: #2d3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }
        .certificate-subheader {
            font-size: 20px;
            color: #5a5a5a;
            margin-bottom: 30px;
        }
        .certificate-name {
            font-size: 48px;
            font-weight: bold;
            color: #3b8b3f;
            margin-bottom: 15px;
        }
        .certificate-course {
            font-size: 40px;
            font-weight: bold;
            color: #2a6eb8;
            margin-bottom: 20px;
        }
        .certificate-footer {
            font-size: 18px;
            color: #4c4c4c;
            margin-top: 30px;
            font-style: italic;
        }
        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            text-align: center;
        }
        .signature {
            font-size: 22px;
            color: #2d3e50;
            font-weight: bold;
            text-align: center;
        }
        .certificate-seal {
            background: #f1c40f;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto;
            position: relative;
        }
        .seal-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }
        .certificate-border {
            border: 5px solid #2a6eb8;
            border-radius: 15px;
            padding: 30px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<div class='certificate-container certificate-border'>
    <h1 class='certificate-header'>Certificate of Completion</h1>
    <p class='certificate-subheader'>This certificate is awarded to</p>
    
    <h2 class='certificate-name'>{$userName}</h2>
    
    <p class='certificate-subheader'>for successfully completing the course</p>
    
    <h2 class='certificate-course'>{$courseName}</h2>
    
    <p class='certificate-footer'>Congratulations on your achievement!</p>
    
    <div class='signature-container'>
        <div class='signature'>
            <p>Instructor's Signature</p>
            <p>_________________</p>
        </div>
        <div class='signature'>
            <p>Authorized Signature</p>
            <p>_________________</p>
        </div>
    </div>
    
    <div class='certificate-seal'>
        <span class='seal-text'>Certified</span>
    </div>
</div>

</body>
</html>

        ";
    }
}