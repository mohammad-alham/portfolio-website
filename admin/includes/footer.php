<?php if (basename($_SERVER['PHP_SELF']) !== 'login.php'): ?>
        </div> <!-- /p-6 -->
    </div> <!-- /main-content -->
    <script>
        // Confirm delete
        document.querySelectorAll('.confirm-delete').forEach(el => {
            el.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });

        // Auto-hide flash messages
        document.querySelectorAll('.flash').forEach(el => {
            setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.5s'; }, 4000);
            setTimeout(() => { el.remove(); }, 4500);
        });
    </script>
<?php endif; ?>
</body>
</html>
