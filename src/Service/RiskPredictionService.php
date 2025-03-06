<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

class RiskPredictionService
{
    private $modelPath;
    private $scalerPath;
    private $labelEncoderPath;
    private $logger;

    public function __construct(ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->modelPath = $params->get('kernel.project_dir') . '/assets/models/risk_prediction_model.joblib';
        $this->scalerPath = $params->get('kernel.project_dir') . '/assets/models/risk_scaler.joblib';
        $this->labelEncoderPath = $params->get('kernel.project_dir') . '/assets/models/risk_label_encoder.joblib';
        $this->logger = $logger;
    }

    public function predictRisk(array $invoiceData): string
    {
        // Convertir les données en JSON
        $jsonData = json_encode($invoiceData);

        // Appeler le script Python
        $command = "python3 " . escapeshellarg(__DIR__ . '/../../assets/models/predict_risk.py') . " " . escapeshellarg($jsonData);
        $riskLevel = shell_exec($command);

        // Logger la commande et le résultat
        $this->logger->info('Command executed: ' . $command);
        $this->logger->info('Risk level predicted: ' . $riskLevel);

        return trim($riskLevel); // Retirer les espaces et sauts de ligne
    }
}