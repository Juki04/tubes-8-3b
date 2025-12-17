const API_URL = "../api/bundle_creator.php";

const form = document.getElementById("budget-form");
const resultsContainer = document.getElementById("results-container");
const summaryContainer = document.getElementById("summary");

// Show loading state
function showLoading() {
    resultsContainer.innerHTML = `
        <div class="loading">
            <div class="spinner"></div>
            <p>Mengambil rekomendasi bundle...</p>
        </div>
    `;
}

// Show error message
function showError(message) {
    resultsContainer.innerHTML = `
        <div class="error-box">
            <strong>Terjadi kesalahan:</strong> ${message}
        </div>
    `;
}

// Debug: Log when the script loads
console.log('Script loaded, setting up form listener...');

form.addEventListener("submit", async (e) => {
    e.preventDefault();
    
    console.log('Form submitted');
    
    const budgetUsd = form.elements['budget_usd'].value;
    const bundleType = form.elements['bundle_type'].value.toUpperCase();
    
    console.log('Form values:', { budgetUsd, bundleType });
    
    // Validate input
    if (!budgetUsd || budgetUsd <= 0) {
        showError("Masukkan budget yang valid");
        return;
    }
    
    if (!bundleType) {
        showError("Pilih tipe bundle");
        return;
    }
    
    showLoading();
    
    try {
        const apiUrl = `${API_URL}?bundle_type=${encodeURIComponent(bundleType)}&budget_usd=${encodeURIComponent(budgetUsd)}`;
        console.log('Fetching from:', apiUrl);
        
        const response = await fetch(apiUrl);
        console.log('Response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP Error ${response.status}`);
        }

        const data = await response.json();

        if (data.status !== "success") {
            throw new Error(data.message || "Terjadi kesalahan saat memproses permintaan");
        }

        renderSummary(data);
        
        // Combine purchased and ignored items for display
        const allItems = [
            ...(data.items_purchased || []).map(item => ({ ...item, status_purchase: "Dibeli" })),
            ...(data.items_ignored || []).map(item => ({ ...item, status_purchase: "Diabaikan" }))
        ];

        renderTable(allItems);

    } catch (err) {
        console.error("Error:", err);
        showError(err.message || "Terjadi kesalahan yang tidak diketahui");
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
                <td>${item.name || 'N/A'}</td>
                <td>${item.price_idr ? item.price_idr.toLocaleString("id-ID") : 'N/A'}</td>
                <td>
                    ${item.link_ebay ? 
                        `<a href="${item.link_ebay}" target="_blank" class="ebay-link">Lihat di eBay</a>` : 
                        'Tidak tersedia'}
                </td>
                <td>
                    <span class="${badgeClass}">
                        ${item.status_purchase || 'N/A'}
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
