<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-5">
    <h2>Gestion des utilisateurs</h2>
    <a href="/admin" class="btn btn-secondary mb-3">← Retour au dashboard</a>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
