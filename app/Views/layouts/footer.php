    </main>

    <!-- Footer -->
    <footer class="site-footer text-center py-4 mt-5">
        <p class="mb-1"><strong>Horaires :</strong> <?php
            use App\Models\Horaire;
            echo htmlspecialchars(Horaire::getHorairesFormatted());
        ?></p>
        <p class="mb-2">
            <a href="/contact">Contact</a> · 
            <a href="/">Accueil</a> · 
            <a href="/mentions-legales">Mentions légales</a> · 
            <a href="/cgv">CGV</a>
        </p>
        <small>© <?= date('Y') ?> Vite & Gourmand</small>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JS personnalisé -->
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/validation.js"></script>
    <script src="/assets/js/commandes.js"></script>
    <script src="/assets/js/avis.js"></script>
    <script src="/assets/js/menus.js"></script>
    
    <?php if (isset($additionalScripts) && is_array($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
