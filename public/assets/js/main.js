const API_URL = "../api/bundle_creator.php";

const form = document.getElementById("budget-form");
const resultsContainer = document.getElementById("results-container");
const summaryContainer = document.getElementById("summary");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const budgetUsd = form.budget_usd.value;
    const bundleType = form.bundle_type.value;

    resultsContainer.innerHTML = `<div class="loading">Memproses...</div>`;
    summaryContainer.innerHTML = "";

    try {
        const response = await fetch(
            `${API_URL}?bundle_type=${encodeURIComponent(bundleType)}&budget_usd=${encodeURIComponent(budgetUsd)}`
        );

        if (!response.ok) {
            throw new Error("HTTP Error " + response.status);
        }

        const data = await response.json();

        if (data.status !== "success") {
            throw new Error(data.message);
        }

        // 🔥 PANGGIL FUNGSI (INI YANG SEBELUMNYA HILANG)
        renderSummary(data);

        // Gabungkan item dibeli + diabaikan (SESUIAI BACKEND TIM A)
        const allItems = [
            ...data.items_purchased,
            ...data.items_ignored
        ];

        renderTable(allItems);

    } catch (err) {
        resultsContainer.innerHTML = `
            <div class="error-box">
                ${err.message}
            </div>
        `;
    }
});

// ================= RENDER SUMMARY =================
function renderSummary(data) {
    summaryContainer.innerHTML = `
        <div class="card summary-grid">
            <div>
                <span>Budget Awal (USD)</span>
                <strong>$${data.initial_budget_usd}</strong>
            </div>
            <div>
                <span>Total Belanja (IDR)</span>
                <strong>${data.total_spent_idr.toLocaleString("id-ID")}</strong>
            </div>
            <div>
                <span>Sisa Budget (USD)</span>
                <strong>$${data.remaining_budget_usd}</strong>
            </div>
        </div>
    `;
}

// ================= RENDER TABLE =================
function renderTable(items) {
    if (!items || items.length === 0) {
        resultsContainer.innerHTML = `
            <div class="error-box">
                Tidak ada item yang dapat ditampilkan
            </div>
        `;
        return;
    }

    let rows = "";

    items.forEach(item => {
        const badgeClass =
            item.status_purchase === "Dibeli"
                ? "badge success"
                : "badge danger";

        rows += `
            <tr>
                <td>${item.name}</td>
                <td>${item.price_idr.toLocaleString("id-ID")}</td>
                <td>-</td>
                <td>
                    <span class="${badgeClass}">
                        ${item.status_purchase}
                    </span>
                </td>
            </tr>
        `;
    });

    resultsContainer.innerHTML = `
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Harga (IDR)</th>
                        <th>Link</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows}
                </tbody>
            </table>
        </div>
    `;
}
