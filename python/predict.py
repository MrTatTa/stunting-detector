import sys
import json
import joblib
import pandas as pd
import os

# =========================
# BASE DIRECTORY (AMAN)
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))

MODEL_PATH = os.path.join(BASE_DIR, "model", "rf_stunting.pkl")
FEATURE_PATH = os.path.join(BASE_DIR, "model", "feature_importance.json")

# =========================
# VALIDASI ARGUMEN
# =========================
# format: python predict.py usia tinggi_badan lila hb
if len(sys.argv) != 5:
    print(json.dumps({
        "error": "Parameter tidak lengkap. Format: usia tinggi_badan lila hb"
    }))
    sys.exit(1)

try:
    usia = float(sys.argv[1])
    tinggi = float(sys.argv[2])
    lila = float(sys.argv[3])
    hb = float(sys.argv[4])
except ValueError:
    print(json.dumps({
        "error": "Semua parameter harus berupa angka"
    }))
    sys.exit(1)

# =========================
# LOAD MODEL & FEATURE IMPORTANCE
# =========================
if not os.path.exists(MODEL_PATH):
    print(json.dumps({"error": "Model tidak ditemukan"}))
    sys.exit(1)

model = joblib.load(MODEL_PATH)

with open(FEATURE_PATH) as f:
    faktor = json.load(f)

# =========================
# PREPARE DATA (SAMA DENGAN TRAINING)
# =========================
feature_names = ["usia", "tinggi_badan", "lila", "hb"]

X = pd.DataFrame(
    [[usia, tinggi, lila, hb]],
    columns=feature_names
)

# =========================
# PREDIKSI
# =========================
pred = model.predict(X)[0]
proba = model.predict_proba(X)[0]

hasil = "STUNTING" if pred == 1 else "NORMAL"
probabilitas_stunting = float(proba[1])  # kelas 1 = stunting

# =========================
# OUTPUT JSON (BERSIH)
# =========================
output = {
    "hasil": hasil,
    "probabilitas": round(probabilitas_stunting, 4),
    "faktor": faktor
}

print(json.dumps(output))
