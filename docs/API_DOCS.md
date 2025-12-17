# Bundle Creator API Documentation

## Endpoint Utama

GET /api/bundle_creator.php

Endpoint ini digunakan untuk menghasilkan rekomendasi bundle produk berdasarkan tipe bundle dan budget pengguna.

---

## Request

*Method:* GET
*URL:* /api/bundle_creator.php

### Query Parameters

| Parameter     | Tipe   | Wajib | Deskripsi                                                         |
| ------------- | ------ | ----- | ----------------------------------------------------------------- |
| bundle_type | string | Ya    | Jenis bundle yang diminta (contoh: gaming, office, design). |
| budget_usd  | number | Ya    | Batas budget dalam USD.                                           |

*Contoh Request*


GET /api/bundle_creator.php?bundle_type=gaming&budget_usd=1200


---

## Response

### Response Sukses (200 OK)

json
{
  "status": "success",
  "bundle_type": "gaming",
  "budget_usd": 1200,
  "total_price": 1180,
  "items": [
    {
      "id": 1,
      "name": "Processor Ryzen 5",
      "category": "CPU",
      "price": 250,
      "status": "dibeli"
    },
    {
      "id": 2,
      "name": "RTX 3060",
      "category": "GPU",
      "price": 450,
      "status": "dibeli"
    }
  ],
  "message": "Bundle berhasil dibuat"
}


### Response Gagal (400 / 500)

json
{
  "status": "error",
  "message": "Parameter tidak lengkap atau budget tidak mencukupi"
}


### Catatan Error

* Parameter tidak lengkap
* Budget tidak mencukupi
* Kesalahan server internal