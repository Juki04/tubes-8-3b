<?php
// =======================================================
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bundle Creator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="app-container">

        <header class="app-header">
            <h1>Bundle Creator</h1>
            <p>Rekomendasi bundle berdasarkan budget Anda</p>
        </header>

        <!-- Form -->
        <div class="card">
            <form id="budget-form" class="form-grid">
                <div class="form-group">
                    <label>Budget (USD)</label>
                    <input type="number" name="budget_usd" placeholder="Contoh: 500" required>
                </div>

                <div class="form-group">
                    <label>Tipe Bundle</label>
                    <select name="bundle_type" required>
                        <option value="">-- Pilih --</option>
                        <option value="ISI_KAMAR_KOS">Isi Kamar Kos</option>
                        <option value="GAMING_DASAR">Gaming Dasar</option>
                    </select>
                </div>

                <div class="form-action">
                    <button type="submit">Generate Bundle</button>
                </div>
            </form>
        </div>

        <!-- Ringkasan -->
        <div id="summary" class="summary-card"></div>

        <!-- Hasil -->
        <div id="results-container"></div>

    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>

