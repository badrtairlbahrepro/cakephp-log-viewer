<?php

declare(strict_types=1);

namespace LogViewer\Controller;

use Cake\Controller\Controller;

/**
 * LogViewer Controller
 *
 * Interface Telescope-like pour consulter et gérer les fichiers de log.
 * Permet de voir, filtrer et télécharger les logs en direct.
 */
class LogsController extends Controller
{
    /**
     * Répertoire des logs
     */
    private string $logsDir;

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        
        // Load paths configuration if LOGS constant is not defined
        if (!defined('LOGS')) {
            $pathsFile = ROOT . DS . 'config' . DS . 'paths.php';
            if (file_exists($pathsFile)) {
                require $pathsFile;
            }
        }
        
        // Set logs directory
        $this->logsDir = defined('LOGS') ? LOGS : (ROOT . DS . 'logs' . DS);
    }

    /**
     * Lister tous les fichiers de log disponibles
     *
     * @return void
     */
    public function index(): void
    {
        $logFiles = [];

        // Récupérer tous les fichiers de log
        if (is_dir($this->logsDir)) {
            $files = scandir($this->logsDir);
            if ($files !== false) {
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..' && substr($file, -4) === '.log') {
                        $filePath = $this->logsDir . $file;
                        $logFiles[] = [
                            'name' => $file,
                            'path' => $filePath,
                            'size' => filesize($filePath),
                            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                        ];
                    }
                }
            }
        }

        // Sort by modification date (newest first)
        usort($logFiles, function ($a, $b) {
            return $b['modified'] <=> $a['modified'];
        });

        $this->set(compact('logFiles'));
    }

    /**
     * Afficher le contenu d'un fichier de log spécifique
     *
     * @param string|null $filename Le nom du fichier de log
     * @return \Cake\Http\Response|null
     */
    public function view(?string $filename = null): ?\Cake\Http\Response
    {
        // Lire et afficher un fichier de log
        // Prevent directory traversal
        $filename = $filename ? basename($filename) : '';
        if (substr($filename, -4) !== '.log') {
            $filename .= '.log';
        }

        $filePath = $this->logsDir . $filename;

        if (!file_exists($filePath)) {
            $this->Flash->error('Fichier de log non trouvé: ' . $filename);
            return $this->redirect(['plugin' => 'LogViewer', 'action' => 'index']) ?? $this->response;
        }

        // Get file content
        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->Flash->error('Impossible de lire le fichier de log');
            return $this->redirect(['plugin' => 'LogViewer', 'action' => 'index']) ?? $this->response;
        }

        $lines = explode("\n", trim($content));

        // Get date filter parameters
        $dateFrom = $this->request->getQuery('date_from', '');
        $dateTo = $this->request->getQuery('date_to', '');

        // Filter by date if provided
        if (!empty($dateFrom) || !empty($dateTo)) {
            $lines = array_filter($lines, function ($line) use ($dateFrom, $dateTo) {
                if (empty(trim($line))) {
                    return false;
                }
                
                // Extract timestamp from line
                if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
                    $logDate = strtotime($matches[1]);
                    
                    if (!empty($dateFrom) && $logDate < strtotime($dateFrom)) {
                        return false;
                    }
                    if (!empty($dateTo) && $logDate > strtotime($dateTo . ' 23:59:59')) {
                        return false;
                    }
                    
                    return true;
                }
                
                return true;
            });
            
            // Re-index array
            $lines = array_values($lines);
        }

        // Get pagination parameters
        $page = (int)$this->request->getQuery('page', 1);
        $perPage = 100;
        $totalLines = count($lines);
        $totalPages = ceil($totalLines / $perPage);

        // Validate page
        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        // Get page lines (reverse order - newest first)
        $startIndex = ($totalPages - $page) * $perPage;
        $pageLines = array_reverse(array_slice($lines, max(0, $totalLines - ($page * $perPage)), $perPage));

        // Parse log lines to extract timestamp, level, and message
        $parsedLines = [];
        foreach ($pageLines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $parsed = $this->parseLogLine($line);
            $parsedLines[] = $parsed;
        }

        // Get statistics
        $stats = $this->calculateStats($lines);

        $this->set(compact('filename', 'parsedLines', 'page', 'totalPages', 'totalLines', 'stats', 'dateFrom', 'dateTo'));
        return null;
    }

    /**
     * Calculer les statistiques des logs
     *
     * @param array<string> $lines Lignes de log
     * @return array<string, int>
     */
    private function calculateStats(array $lines): array
    {
        $stats = [
            'total' => 0,
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0,
            'critical' => 0,
            'notice' => 0,
        ];

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $parsed = $this->parseLogLine($line);
            $stats['total']++;
            
            if (isset($stats[$parsed['level']])) {
                $stats[$parsed['level']]++;
            }
        }

        return $stats;
    }

    /**
     * Exporter les logs en CSV ou JSON
     *
     * @param string|null $filename Le nom du fichier de log
     * @return \Cake\Http\Response
     */
    public function export(?string $filename = null): \Cake\Http\Response
    {
        // Prevent directory traversal
        $filename = $filename ? basename($filename) : '';
        if (substr($filename, -4) !== '.log') {
            $filename .= '.log';
        }

        $filePath = $this->logsDir . $filename;

        if (!file_exists($filePath)) {
            $this->Flash->error('Fichier de log non trouvé: ' . $filename);
            return $this->redirect(['plugin' => 'LogViewer', 'action' => 'index']) ?? $this->response;
        }

        // Get file content
        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->Flash->error('Impossible de lire le fichier de log');
            return $this->redirect(['plugin' => 'LogViewer', 'action' => 'index']) ?? $this->response;
        }

        $lines = explode("\n", trim($content));

        // Get date filter parameters
        $dateFrom = $this->request->getQuery('date_from', '');
        $dateTo = $this->request->getQuery('date_to', '');

        // Filter by date if provided
        if (!empty($dateFrom) || !empty($dateTo)) {
            $lines = array_filter($lines, function ($line) use ($dateFrom, $dateTo) {
                if (empty(trim($line))) {
                    return false;
                }
                
                if (preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
                    $logDate = strtotime($matches[1]);
                    
                    if (!empty($dateFrom) && $logDate < strtotime($dateFrom)) {
                        return false;
                    }
                    if (!empty($dateTo) && $logDate > strtotime($dateTo . ' 23:59:59')) {
                        return false;
                    }
                    
                    return true;
                }
                
                return true;
            });
            
            $lines = array_values($lines);
        }

        // Parse all lines
        $parsedLines = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $parsed = $this->parseLogLine($line);
            $parsedLines[] = $parsed;
        }

        // Get format (csv or json)
        $format = $this->request->getQuery('format', 'csv');

        if ($format === 'json') {
            // Export as JSON
            $response = $this->response
                ->withType('application/json')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '.json"');
            
            return $response->withStringBody(json_encode($parsedLines, JSON_PRETTY_PRINT));
        }

        // Export as CSV
        $csv = "Timestamp,Level,Message\n";
        foreach ($parsedLines as $line) {
            $csv .= sprintf(
                '"%s","%s","%s"' . "\n",
                $line['timestamp'],
                $line['level'],
                str_replace('"', '""', $line['message'])
            );
        }

        $response = $this->response
            ->withType('text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');

        return $response->withStringBody($csv);
    }

    /**
     * Parser et colorer une ligne de log
     * 
     * @param string $line Ligne de log
     * @return array<string, mixed>
     */
    private function parseLogLine(string $line): array
    {
        // Analyser et formater une ligne de log
        $level = 'info';
        $timestamp = date('Y-m-d H:i:s');
        $message = $line;

        // Try to extract timestamp
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $line, $matches)) {
            $timestamp = $matches[0];
            $message = substr($line, strlen($timestamp) + 1);
        }

        // Try to extract level from CakePHP log format
        // Format: YYYY-MM-DD HH:MM:SS [LEVEL] message
        
        // Method 1: Extract from brackets [ERROR]
        if (preg_match('/\[(ERROR|WARNING|DEBUG|INFO|NOTICE|CRITICAL)\]/i', $message, $matches)) {
            $level = strtolower($matches[1]);
        }
        // Method 2: Check if line contains error keywords
        elseif (preg_match('/\b(error|exception|fatal|failure|failed)\b/i', $message)) {
            $level = 'error';
        }
        // Method 3: Check for warning keywords
        elseif (preg_match('/\b(warning|warn|deprecated|deprecation)\b/i', $message)) {
            $level = 'warning';
        }
        // Method 4: Check for debug keywords
        elseif (preg_match('/\b(debug|trace|traceback)\b/i', $message)) {
            $level = 'debug';
        }
        // Method 5: Check for critical keywords
        elseif (preg_match('/\b(critical|emergency|panic|alert)\b/i', $message)) {
            $level = 'critical';
        }
        // Method 6: Check for notice keywords
        elseif (preg_match('/\b(notice|informational)\b/i', $message)) {
            $level = 'notice';
        }

        // Determine color based on level
        switch ($level) {
            case 'error':
                $color = '#dc3545';
                break;
            case 'warning':
                $color = '#ffc107';
                break;
            case 'debug':
                $color = '#6c757d';
                break;
            case 'critical':
                $color = '#c82333';
                break;
            case 'notice':
                $color = '#17a2b8';
                break;
            default:
                $color = '#28a745';
        }

        return [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => substr($message, 0, 500), // Limit message length
            'color' => $color,
        ];
    }

    /**
     * Vider un fichier de log
     *
     * @param string|null $filename Le nom du fichier
     * @return \Cake\Http\Response
     */
    public function clear(?string $filename = null): \Cake\Http\Response
    {
        // Supprimer le contenu d'un fichier de log
        if (!$this->request->is('post')) {
            $this->Flash->error('Méthode de requête invalide');
            return $this->redirect(['plugin' => 'LogViewer', 'action' => 'index']) ?? $this->response;
        }

        // Prevent directory traversal
        $filename = $filename ? basename($filename) : '';
        if (substr($filename, -4) !== '.log') {
            $filename .= '.log';
        }

        $filePath = $this->logsDir . $filename;

        if (file_exists($filePath)) {
            file_put_contents($filePath, '');
            $this->Flash->success('Fichier de log vidé: ' . $filename);
        } else {
            $this->Flash->error('Fichier de log non trouvé: ' . $filename);
        }

        return $this->redirect(['plugin' => 'LogViewer', 'action' => 'view', $filename]) ?? $this->response;
    }

    /**
     * Télécharger un fichier de log
     *
     * @param string|null $filename Le nom du fichier
     * @return \Cake\Http\Response
     */
    public function download(?string $filename = null): \Cake\Http\Response
    {
        // Télécharger un fichier de log sur l'ordinateur
        // Prevent directory traversal
        $filename = $filename ? basename($filename) : '';
        if (substr($filename, -4) !== '.log') {
            $filename .= '.log';
        }

        $filePath = $this->logsDir . $filename;

        if (!file_exists($filePath)) {
            $this->Flash->error('Fichier de log non trouvé: ' . $filename);
            return $this->redirect(['plugin' => 'LogViewer', 'action' => 'index']) ?? $this->response;
        }

        // Set response for download
        $this->response = $this->response
            ->withType('text/plain')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Read and return file content
        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->Flash->error('Impossible de lire le fichier de log');
            return $this->redirect(['plugin' => 'LogViewer', 'action' => 'index']) ?? $this->response;
        }

        return $this->response->withStringBody($content);
    }
}
