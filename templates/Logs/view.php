<?php
/**
 * Logs View Template
 * Display individual log file with pagination and filtering
 */
$this->Html->css(['https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css'], ['block' => 'css']);
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0">
                    📋 <?= h($filename) ?>
                    <span class="badge badge-secondary"><?= $totalLines ?> lines</span>
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <?= $this->Html->link(
                    '<i class="fas fa-arrow-left"></i> Back',
                    ['action' => 'index'],
                    ['class' => 'btn btn-secondary btn-sm', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-download"></i> Download',
                    ['action' => 'download', str_replace('.log', '', $filename)],
                    ['class' => 'btn btn-success btn-sm', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-file-csv"></i> Export CSV',
                    ['action' => 'export', str_replace('.log', '', $filename), '?' => ['format' => 'csv', 'date_from' => $dateFrom, 'date_to' => $dateTo]],
                    ['class' => 'btn btn-info btn-sm', 'escape' => false]
                ) ?>
                <?= $this->Html->link(
                    '<i class="fas fa-file-code"></i> Export JSON',
                    ['action' => 'export', str_replace('.log', '', $filename), '?' => ['format' => 'json', 'date_from' => $dateFrom, 'date_to' => $dateTo]],
                    ['class' => 'btn btn-warning btn-sm', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logSearch = document.getElementById('logSearch');
    const levelFilter = document.getElementById('levelFilter');
    const clearFilters = document.getElementById('clearFilters');
    const resultCount = document.getElementById('resultCount');
    const scrollToTop = document.getElementById('scrollToTop');
    const logEntries = document.querySelectorAll('.log-entry');

    function filterLogs() {
        const searchTerm = logSearch.value.toLowerCase().trim();
        const selectedLevel = levelFilter.value.toLowerCase().trim();
        let visibleCount = 0;

        logEntries.forEach(entry => {
            const message = entry.dataset.message || '';
            const level = entry.dataset.level || '';
            
            const matchesSearch = !searchTerm || message.includes(searchTerm);
            const matchesLevel = !selectedLevel || level === selectedLevel;
            
            if (matchesSearch && matchesLevel) {
                entry.style.display = 'flex';
                visibleCount++;
            } else {
                entry.style.display = 'none';
            }
        });

        // Update result count
        if (searchTerm || selectedLevel) {
            resultCount.style.display = 'inline-block';
            resultCount.textContent = `${visibleCount} résultat(s)`;
        } else {
            resultCount.style.display = 'none';
        }
    }

    // Event listeners
    logSearch.addEventListener('input', filterLogs);
    levelFilter.addEventListener('change', filterLogs);
    
    clearFilters.addEventListener('click', function() {
        logSearch.value = '';
        levelFilter.value = '';
        filterLogs();
    });

    scrollToTop.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>

<div class="content">
    <div class="container-fluid">
        <!-- Statistics Panel -->
        <?php if (isset($stats) && is_array($stats)): ?>
        <div class="row mb-3">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $stats['error'] ?? 0 ?></h3>
                        <p>Erreurs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $stats['warning'] ?? 0 ?></h3>
                        <p>Avertissements</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $stats['info'] ?? 0 ?></h3>
                        <p>Info</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?= $stats['debug'] ?? 0 ?></h3>
                        <p>Debug</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bug"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-gradient-red">
                    <div class="inner">
                        <h3><?= $stats['critical'] ?? 0 ?></h3>
                        <p>Critique</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-fire"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $stats['total'] ?? 0 ?></h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Search and Filter Panel -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search"></i>
                    Recherche et filtres
                </h3>
            </div>
            <div class="card-body">
                <form method="get" action="<?= $this->Url->build(['action' => 'view', str_replace('.log', '', $filename)]) ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from">
                                    <i class="fas fa-calendar"></i> Date de début
                                </label>
                                <input type="date" 
                                       id="date_from" 
                                       name="date_from" 
                                       class="form-control" 
                                       value="<?= h($dateFrom ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to">
                                    <i class="fas fa-calendar"></i> Date de fin
                                </label>
                                <input type="date" 
                                       id="date_to" 
                                       name="date_to" 
                                       class="form-control" 
                                       value="<?= h($dateTo ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filtrer
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <a href="<?= $this->Url->build(['action' => 'view', str_replace('.log', '', $filename)]) ?>" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="logSearch">
                                <i class="fas fa-search"></i> Rechercher dans les logs
                            </label>
                            <input type="text" 
                                   id="logSearch" 
                                   class="form-control" 
                                   placeholder="Tapez pour rechercher...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-filter"></i> Filtrer par niveau
                            </label>
                            <select id="levelFilter" class="form-control">
                                <option value="">Tous les niveaux</option>
                                <option value="error">❌ Error</option>
                                <option value="warning">⚠️ Warning</option>
                                <option value="critical">🔴 Critical</option>
                                <option value="debug">🐛 Debug</option>
                                <option value="info">ℹ️ Info</option>
                                <option value="notice">💡 Notice</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-times-circle"></i> Actions
                            </label>
                            <div>
                                <button id="clearFilters" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo"></i> Réinitialiser
                                </button>
                                <span id="resultCount" class="badge badge-info ml-2" style="display: none;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Entrées de log (Page <?= $page ?> sur <?= max(1, $totalPages) ?>)
                </h3>
                <div class="card-tools">
                    <button id="scrollToTop" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-up"></i> Haut
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div style="max-height: 600px; overflow-y: auto; font-family: monospace; font-size: 0.85rem; background: #1e1e1e; color: #d4d4d4;">
                    <?php foreach ($parsedLines as $index => $log): ?>
                        <div class="log-entry" 
                             data-level="<?= h(strtolower($log['level'])) ?>"
                             data-message="<?= h(strtolower($log['message'])) ?>"
                             style="padding: 8px 12px; border-bottom: 1px solid #333; display: flex; align-items: center;">
                            <!-- Line Number -->
                            <span style="color: #858585; width: 50px; text-align: right; margin-right: 12px; flex-shrink: 0;">
                                <?= ($totalLines - (($page - 1) * 100) - $index) ?>
                            </span>
                            
                            <!-- Timestamp -->
                            <span style="color: #6A9955; width: 180px; flex-shrink: 0;">
                                <?= h($log['timestamp']) ?>
                            </span>
                            
                            <!-- Level Badge -->
                            <span class="log-level"
                                  style="
                                background: <?= $log['color'] ?>;
                                color: white;
                                padding: 2px 8px;
                                border-radius: 4px;
                                width: 70px;
                                text-align: center;
                                margin-left: 8px;
                                font-weight: bold;
                                font-size: 0.8rem;
                                flex-shrink: 0;
                            ">
                                <?= strtoupper($log['level']) ?>
                            </span>
                            
                            <!-- Message -->
                            <span class="log-message" style="color: #CE9178; margin-left: 12px; word-break: break-word; flex-grow: 1;">
                                <?= h($log['message']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm m-0">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <?= $this->Html->link(
                                    'Previous',
                                    ['action' => 'view', str_replace('.log', '', $filename), '?' => ['page' => $page - 1]],
                                    ['class' => 'page-link']
                                ) ?>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">Previous</span>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <?php if ($i === $page): ?>
                                <li class="page-item active">
                                    <span class="page-link"><?= $i ?></span>
                                </li>
                            <?php else: ?>
                                <li class="page-item">
                                    <?= $this->Html->link(
                                        $i,
                                        ['action' => 'view', str_replace('.log', '', $filename), '?' => ['page' => $i]],
                                        ['class' => 'page-link']
                                    ) ?>
                                </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <?= $this->Html->link(
                                    'Next',
                                    ['action' => 'view', str_replace('.log', '', $filename), '?' => ['page' => $page + 1]],
                                    ['class' => 'page-link']
                                ) ?>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link">Next</span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
