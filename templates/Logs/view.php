<?php
/**
 * Logs View Template - Bootstrap 5 Standalone
 * Display individual log file with pagination and filtering
 */
// Bootstrap 5 CSS
$this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', ['block' => true]);
// Font Awesome
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', ['block' => true]);
// Highlight.js
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css', ['block' => true]);
?>
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --info-gradient: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    --success-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

body {
    background: linear-gradient(to right, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.log-viewer-header {
    background: var(--primary-gradient);
    color: white;
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    margin-bottom: 2rem;
}

.log-card {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    overflow: hidden;
}

.stat-card {
    text-align: center;
    padding: 1.5rem;
    border-radius: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    color: white;
    border: none;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.stat-card.error { background: var(--danger-gradient); }
.stat-card.warning { background: var(--warning-gradient); }
.stat-card.info { background: var(--info-gradient); }
.stat-card.debug { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.critical { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card.success { background: var(--success-gradient); color: #333 !important; }

.stat-card h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.stat-card p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

.search-panel {
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.log-entries-container {
    background: #1e1e1e;
    border-radius: 1rem;
    overflow: hidden;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    max-height: 600px;
    overflow-y: auto;
}

.log-entry {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #333;
    transition: background-color 0.2s ease;
}

.log-entry:hover {
    background-color: #2a2a2a;
}

.log-line-number {
    color: #858585;
    width: 50px;
    text-align: right;
    margin-right: 1rem;
    flex-shrink: 0;
}

.log-timestamp {
    color: #6A9955;
    width: 180px;
    flex-shrink: 0;
}

.log-level-badge {
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    width: 80px;
    text-align: center;
    margin: 0 1rem;
    font-weight: bold;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.log-message {
    color: #CE9178;
    word-break: break-word;
    flex-grow: 1;
}

.btn {
    border-radius: 0.5rem;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
    border-radius: 0.5rem;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
    padding: 0.5rem 1rem;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

#scrollToTop {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

#resultCount {
    display: none;
}
</style>

<!-- Header -->
<div class="log-viewer-header">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="mb-2">
                <i class="fas fa-file-code"></i> <?= h($filename) ?>
            </h1>
            <p class="mb-0">
                <span class="badge bg-light text-dark"><?= $totalLines ?> lignes</span>
            </p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <?= $this->Html->link(
                '<i class="fas fa-arrow-left"></i> Retour',
                ['action' => 'index'],
                ['class' => 'btn btn-light me-2', 'escape' => false]
            ) ?>
            <?= $this->Html->link(
                '<i class="fas fa-download"></i> Télécharger',
                ['action' => 'download', str_replace('.log', '', $filename)],
                ['class' => 'btn btn-success', 'escape' => false]
            ) ?>
            <?= $this->Html->link(
                '<i class="fas fa-file-csv"></i> CSV',
                ['action' => 'export', str_replace('.log', '', $filename), '?' => ['format' => 'csv', 'date_from' => $dateFrom, 'date_to' => $dateTo]],
                ['class' => 'btn btn-info', 'escape' => false]
            ) ?>
            <?= $this->Html->link(
                '<i class="fas fa-file-code"></i> JSON',
                ['action' => 'export', str_replace('.log', '', $filename), '?' => ['format' => 'json', 'date_from' => $dateFrom, 'date_to' => $dateTo]],
                ['class' => 'btn btn-warning', 'escape' => false]
            ) ?>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Statistics -->
    <?php if (isset($stats) && is_array($stats)): ?>
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card error">
                <h2><?= $stats['error'] ?? 0 ?></h2>
                <p><i class="fas fa-exclamation-triangle"></i> Erreurs</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card warning">
                <h2><?= $stats['warning'] ?? 0 ?></h2>
                <p><i class="fas fa-exclamation-circle"></i> Avertissements</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card info">
                <h2><?= $stats['info'] ?? 0 ?></h2>
                <p><i class="fas fa-info-circle"></i> Info</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card debug">
                <h2><?= $stats['debug'] ?? 0 ?></h2>
                <p><i class="fas fa-bug"></i> Debug</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card critical">
                <h2><?= $stats['critical'] ?? 0 ?></h2>
                <p><i class="fas fa-fire"></i> Critique</p>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6 mb-3">
            <div class="stat-card success">
                <h2><?= $stats['total'] ?? 0 ?></h2>
                <p><i class="fas fa-list"></i> Total</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search and Filter Panel -->
    <div class="search-panel">
        <h5 class="mb-4"><i class="fas fa-filter"></i> Filtres et recherche</h5>
        
        <!-- Date Filter -->
        <form method="get" action="<?= $this->Url->build(['action' => 'view', str_replace('.log', '', $filename)]) ?>">
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <label for="date_from" class="form-label"><i class="fas fa-calendar"></i> Date de début</label>
                    <input type="date" id="date_from" name="date_from" class="form-control" value="<?= h($dateFrom ?? '') ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_to" class="form-label"><i class="fas fa-calendar"></i> Date de fin</label>
                    <input type="date" id="date_to" name="date_to" class="form-control" value="<?= h($dateTo ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="<?= $this->Url->build(['action' => 'view', str_replace('.log', '', $filename)]) ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
        
        <hr class="my-4">
        
        <!-- Text and Level Filter -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="logSearch" class="form-label"><i class="fas fa-search"></i> Rechercher dans les logs</label>
                <input type="text" id="logSearch" class="form-control" placeholder="Tapez pour rechercher...">
            </div>
            <div class="col-md-4 mb-3">
                <label for="levelFilter" class="form-label"><i class="fas fa-filter"></i> Filtrer par niveau</label>
                <select id="levelFilter" class="form-select">
                    <option value="">Tous les niveaux</option>
                    <option value="error">❌ Error</option>
                    <option value="warning">⚠️ Warning</option>
                    <option value="critical">🔴 Critical</option>
                    <option value="debug">🐛 Debug</option>
                    <option value="info">ℹ️ Info</option>
                    <option value="notice">💡 Notice</option>
                </select>
            </div>
            <div class="col-md-4 mb-3 d-flex align-items-end">
                <button id="clearFilters" class="btn btn-secondary me-2">
                    <i class="fas fa-redo"></i> Réinitialiser
                </button>
                <span id="resultCount" class="badge bg-info"></span>
            </div>
        </div>
    </div>
    
    <!-- Log Entries -->
    <div class="log-card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Entrées de log 
                <span class="badge bg-light text-dark">Page <?= $page ?> sur <?= max(1, $totalPages) ?></span>
            </h5>
        </div>
        <div class="log-entries-container">
            <?php foreach ($parsedLines as $index => $log): ?>
                <div class="log-entry" 
                     data-level="<?= h(strtolower($log['level'])) ?>"
                     data-message="<?= h(strtolower($log['message'])) ?>">
                    <span class="log-line-number">
                        <?= ($totalLines - (($page - 1) * 100) - $index) ?>
                    </span>
                    <span class="log-timestamp"><?= h($log['timestamp']) ?></span>
                    <span class="log-level-badge" style="background: <?= $log['color'] ?>;">
                        <?= strtoupper($log['level']) ?>
                    </span>
                    <span class="log-message"><?= h($log['message']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Pagination -->
        <div class="card-footer bg-dark">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm justify-content-center mb-0">
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

<!-- Scroll to Top Button -->
<button id="scrollToTop" class="btn btn-primary" title="Retour en haut">
    <i class="fas fa-arrow-up"></i>
</button>

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

        if (searchTerm || selectedLevel) {
            resultCount.style.display = 'inline-block';
            resultCount.textContent = `${visibleCount} résultat(s)`;
        } else {
            resultCount.style.display = 'none';
        }
    }

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

    // Show/hide scroll to top button
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTop.style.display = 'block';
        } else {
            scrollToTop.style.display = 'none';
        }
    });
    
    scrollToTop.style.display = 'none';
});
</script>

<?php
// Bootstrap 5 JS
$this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['block' => 'scriptBottom']);
?>
