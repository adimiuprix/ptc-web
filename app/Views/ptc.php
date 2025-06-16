<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>PTC Ads</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.0/dist/sweetalert2.min.css" rel="stylesheet"/>
        <style>
            .card-ptc {
                transition: 0.3s ease;
            }
            .card-ptc:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            }
        </style>
    </head>
    <body class="bg-light">
        <nav class="navbar navbar-dark bg-primary mb-4">
            <div class="container">
                <span class="navbar-brand mb-0 h1">👁️ View Ads & Earn</span>
            </div>
        </nav>

        <div class="container">
            <?php if (empty($ads)) : ?>
            <div class="alert alert-info text-center">
                Tidak ada iklan tersedia saat ini. Silakan kembali nanti 🤗
            </div>
            <?php else : ?>
            <div class="row g-4">
                <?php foreach ($ads as $ad): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-ptc shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= $ad['title'] ?></h5>
                            <p class="card-text text-muted mb-2"><?= esc($ad['description']) ?></p>
                            <p class="fw-bold text-success">Reward: <?= number_format($ad['reward'], 8) ?> 💸</p>
                            <p class="fw-bold text-success">Timer: <?= esc($ad['timer']) ?> second</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <button onclick="window.location = '<?= site_url('ptc/view/' . $ad['id']) ?>'" class="btn btn-sm btn-primary">Lihat Iklan</button>
                                <span class="text-muted small">
                                    <?= $ad['views'] ?>/<?= $ad['total_view'] ?>
                                    view
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Merender flash message sweatalert2 -->
        <?php if (session()->getFlashdata('sweet_message')): ?>
            <?= session()->getFlashdata('sweet_message') ?>
        <?php endif; ?>

    </body>
</html>
