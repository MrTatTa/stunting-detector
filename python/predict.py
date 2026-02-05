import sys
import json
import joblib
import pandas as pd
import mysql.connector
import os

# =========================
# DATABASE CONFIG
# =========================
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "prediksi_stunting"
}

# =========================
# LOAD MODEL & PARAMETER
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "model", "rf_stunting.pkl")
FEATURE_PATH = os.path.join(BASE_DIR, "model", "feature_importance.json")
PARAM_PATH = os.path.join(BASE_DIR, "model", "active_params.json")

if not os.path.exists(MODEL_PATH):
    print(json.dumps({"error": "Model tidak ditemukan"}))
    sys.exit(1)

model = joblib.load(MODEL_PATH)

with open(FEATURE_PATH) as f:
    faktor = json.load(f)

with open(PARAM_PATH) as f:
    feature_names = json.load(f)

# =========================
# VALIDASI ARGUMEN
# =========================
if len(sys.argv) != len(feature_names):
    print(json.dumps({"error": f"Parameter tidak lengkap. Format: {' '.join(feature_names)}"}))
    sys.exit(1)

try:
    values = [float(x) for x in sys.argv[1:]]
except ValueError:
    print(json.dumps({"error": "Semua parameter harus berupa angka"}))
    sys.exit(1)

X = pd.DataFrame([values], columns=feature_names)

# =========================
# PREDIKSI
# =========================
pred = model.predict(X)[0]
proba = model.predict_proba(X)[0]
hasil = "Berisiko Stunting" if pred == 1 else "Tidak Berisiko"
probabilitas_stunting = float(proba[1])

# =========================
# SIMPAN KE DATABASE
# =========================
conn = mysql.connector.connect(**DB_CONFIG)
cursor = conn.cursor()

# Insert ibu_hamil (dummy jika perlu, harus sesuai ID)
# Disarankan: ambil id dari web form input ibu_hamil
ibu_id = 1  # ganti sesuai input nyata

cursor.execute("""
INSERT INTO prediksi (ibu_id, hasil, probabilitas)
VALUES (%s, %s, %s)
""", (ibu_id, hasil, probabilitas_stunting))
prediksi_id = cursor.lastrowid

# Simpan faktor risiko
for param, kontribusi in faktor.items():
    if param in feature_names:
        nilai = X[param].iloc[0]
        cursor.execute("""
        INSERT INTO faktor_risiko (prediksi_id, parameter, nilai, kontribusi)
        VALUES (%s, %s, %s, %s)
        """, (prediksi_id, param, nilai, kontribusi))

conn.commit()
cursor.close()
conn.close()

# =========================
# OUTPUT JSON
# =========================
output = {
    "hasil": hasil,
    "probabilitas": round(probabilitas_stunting, 4),
    "faktor_risiko": faktor
}

print(json.dumps(output))
