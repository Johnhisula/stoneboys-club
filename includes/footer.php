    </main>
    
    <footer class="footer mt-auto py-3 bg-slate-950 border-top border-slate-900 text-center">
        <div class="container text-muted">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> StoneBoysClub. All rights reserved.</p>
            <small>Built with Native PHP, Bootstrap 5, and Custom CSS.</small>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo isset($basePath) ? $basePath : ''; ?>assets/js/main.js"></script>
</body>
</html>
