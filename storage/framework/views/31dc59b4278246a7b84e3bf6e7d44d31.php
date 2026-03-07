

<?php $__env->startSection('title', 'AI Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title">AI Analytics</h1>
    <p class="page-subtitle">Analyze your farm data and get AI-powered recommendations to improve operations</p>
</div>

<div class="agri-card">
    <div class="agri-card-body">
        <p class="text-muted mb-4">AI Analytics considers your farms, bird batches, egg production, egg sales, expenses, tasks, and plantings to suggest what to do better and what to watch out for.</p>

        <form action="<?php echo e(route('ai-analytics.analyze')); ?>" method="POST" class="mb-4">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-primary btn-lg" id="analyzeBtn">
                <i class="fas fa-brain me-2"></i>Analyze farm data & get recommendations
            </button>
        </form>

        <?php if(isset($recommendations)): ?>
            <div class="border-top pt-4 mt-4">
                <?php if(isset($analyzed_at)): ?>
                    <p class="small text-muted mb-2"><i class="fas fa-clock me-1"></i>Analyzed at <?php echo e($analyzed_at->format('M d, Y H:i')); ?></p>
                <?php endif; ?>
                <div class="ai-recommendations bg-light rounded-3 p-4" style="white-space: pre-wrap; font-family: inherit;"><?php echo e($recommendations); ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($recommendations) && str_contains($recommendations, '**')): ?>
<script>
(function() {
    var el = document.querySelector('.ai-recommendations');
    if (!el) return;
    var text = el.textContent;
    var html = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
    el.innerHTML = html;
})();
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\projects\priority_agribusiness\resources\views/ai-analytics/index.blade.php ENDPATH**/ ?>