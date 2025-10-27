<?php
/**
 * Logs Index Template - Bootstrap 5 Standalone
 * List all available log files
 */
// Bootstrap 5 CSS
$this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', ['block' => true]);
// Font Awesome
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', ['block' => true]);
// Custom CSS
?>
<style>
:root {
    --primary-color: #2c3e50;
    --success-color: #27ae60;
    --info-color: #3498db;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
}

body {
    background: #f5f7fa;
    min-height: 100vh;
    padding: 2rem 0;
}

.log-viewer-header {
    background: var(--primary-color);
    color: white;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.log-viewer-header h1 {
    margin: 0;
    font-weight: 600;
    font-size: 2rem;
}

.log-card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}

.log-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.log-card-header {
    background: var(--primary-color);
    color: white;
    padding: 1.25rem;
    border: none;
}

.log-card-header h3 {
    margin: 0;
    font-weight: 600;
}

.table-hover tbody tr {
    transition: background-color 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    padding: 0.35rem 0.75rem;
    font-weight: 500;
    border-radius: 0.25rem;
}

.info-card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    transition: box-shadow 0.2s ease;
    height: 100%;
    background: white;
}

.info-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.info-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.75rem;
    color: white;
    background: var(--info-color);
}

.info-icon.bg-success {
    background: var(--success-color);
}

.info-content {
    flex: 1;
    padding: 1.5rem;
}

.info-content h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.info-content p {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0.5rem 0 0 0;
}

.btn {
    border-radius: 0.25rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
}

code {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    color: #e83e8c;
}

.empty-state {
    text-align: center;
    padding: 3rem;
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: #6c757d;
    font-weight: 500;
}
</style>

<!-- Header -->
<div class="log-viewer-header text-center">
    <h1><i class="fas fa-scroll"></i> Logs Viewer</h1>
    <p class="mb-0 mt-2 opacity-90">Manage and monitor your application logs</p>
</div>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Log Files Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card log-card">
                <div class="log-card-header">
                    <h3><i class="fas fa-file-alt me-2"></i> Available Log Files</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($logFiles)): ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h4>No log files found</h4>
                            <p>Logs will appear here once your application starts logging.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-file me-2"></i>File Name</th>
                                        <th><i class="fas fa-clock me-2"></i>Last Modified</th>
                                        <th><i class="fas fa-hdd me-2"></i>Size</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logFiles as $log): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary">
                                                    <i class="fas fa-file-code me-2"></i><?= h($log['name']) ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-white">
                                                    <i class="fas fa-calendar me-1"></i><?= $log['modified'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= $this->Number->toReadableSize($log['size']) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-eye"></i> View',
                                                    ['action' => 'view', str_replace('.log', '', $log['name'])],
                                                    ['class' => 'btn btn-sm btn-info me-2', 'escape' => false]
                                                ) ?>
                                                <?= $this->Html->link(
                                                    '<i class="fas fa-download"></i>',
                                                    ['action' => 'download', str_replace('.log', '', $log['name'])],
                                                    ['class' => 'btn btn-sm btn-success', 'escape' => false, 'title' => 'Download']
                                                ) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="info-card d-flex">
                <div class="info-icon">
                    <i class="fas fa-terminal"></i>
                </div>
                <div class="info-content">
                    <h5>CLI Command</h5>
                    <p class="text-muted mb-2">View logs in real-time from terminal</p>
                    <code>tail -f logs/error.log</code>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="info-card d-flex">
                <div class="info-icon bg-success">
                    <i class="fas fa-code"></i>
                </div>
                <div class="info-content">
                    <h5>Code Logger</h5>
                    <p class="text-muted mb-2">Add logs directly in your application</p>
                    <code>Log::debug('message')</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Bootstrap 5 JS
$this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['block' => 'scriptBottom']);
?>
