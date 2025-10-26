<?php
/**
 * Logs Index Template
 * List all available log files
 */
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-6">
                <h1 class="m-0">📊 Logs Viewer</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-2"></i>
                            Available Log Files
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($logFiles)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No log files found
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>📄 File</th>
                                            <th>📅 Modified</th>
                                            <th>📊 Size</th>
                                            <th>🎯 Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logFiles as $log): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= h($log['name']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?= $log['modified'] ?></span>
                                                </td>
                                                <td>
                                                    <?= $this->Number->toReadableSize($log['size']) ?>
                                                </td>
                                                <td>
                                                    <?= $this->Html->link(
                                                        '<i class="fas fa-eye"></i> View',
                                                        ['action' => 'view', str_replace('.log', '', $log['name'])],
                                                        ['class' => 'btn btn-sm btn-info', 'escape' => false]
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

        <!-- Info Box -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-terminal"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">CLI Command</span>
                        <span class="info-box-number">View Logs Anytime</span>
                        <p style="font-size: 0.8rem; margin-top: 5px;">
                            <code>tail -f logs/error.log</code>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-code"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Code Logger</span>
                        <span class="info-box-number">Log From Your App</span>
                        <p style="font-size: 0.8rem; margin-top: 5px;">
                            <code>Log::debug('message')</code>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
