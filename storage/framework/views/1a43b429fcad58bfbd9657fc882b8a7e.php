<?php
  use App\Models\Utility;
?>
<footer class="dash-footer">
  <div class="footer-wrapper">
    <div>
      <span class="text-muted" style="text-align: left;">
        &copy; <?php echo e(date('Y')); ?>

        <?php echo e(Utility::getValByName('footer_text') ? Utility::getValByName('footer_text') : config('app.name', 'Taskly')); ?>

      </span>
    </div>
  </div>
</footer>
<?php /**PATH C:\xampp\htdocs\ws-karla\laravel-api\main_task\resources\views/partials/footer.blade.php ENDPATH**/ ?>