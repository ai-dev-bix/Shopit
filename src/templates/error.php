<!-- Error Page -->
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-6">
                <div class="card has-text-centered">
                    <div class="card-content">
                        <div class="mb-4">
                            <?php if ($code == 404): ?>
                                <i class="fas fa-search fa-4x has-text-grey"></i>
                            <?php elseif ($code == 403): ?>
                                <i class="fas fa-ban fa-4x has-text-danger"></i>
                            <?php elseif ($code == 500): ?>
                                <i class="fas fa-exclamation-triangle fa-4x has-text-warning"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-circle fa-4x has-text-info"></i>
                            <?php endif; ?>
                        </div>
                        
                        <h1 class="title is-2">
                            <?php echo htmlspecialchars($title); ?>
                        </h1>
                        
                        <p class="subtitle is-5 has-text-grey mb-4">
                            <?php echo htmlspecialchars($message); ?>
                        </p>
                        
                        <div class="buttons is-centered">
                            <a href="/" class="button is-primary">
                                <i class="fas fa-home mr-2"></i>
                                Go Home
                            </a>
                            
                            <a href="javascript:history.back()" class="button is-light">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Go Back
                            </a>
                        </div>
                        
                        <?php if (DEVELOPMENT_MODE && isset($e)): ?>
                            <div class="mt-4">
                                <details class="has-text-left">
                                    <summary class="has-text-grey is-clickable">Technical Details</summary>
                                    <div class="mt-2 p-3 has-background-light has-text-grey-dark">
                                        <p><strong>Error:</strong> <?php echo htmlspecialchars($e->getMessage()); ?></p>
                                        <p><strong>File:</strong> <?php echo htmlspecialchars($e->getFile()); ?></p>
                                        <p><strong>Line:</strong> <?php echo htmlspecialchars($e->getLine()); ?></p>
                                        <p><strong>Trace:</strong></p>
                                        <pre class="is-size-7"><?php echo htmlspecialchars($e->getTraceAsString()); ?></pre>
                                    </div>
                                </details>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>