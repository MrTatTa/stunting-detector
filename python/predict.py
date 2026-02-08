import sys
import json
import joblib
import pandas as pd
import os

# =========================
# LOAD MODEL & PARAMETER
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "model", "rf_stunting.pkl")
FEATURE_PATH = os.path.join(BASE_DIR, "model", "feature_importance.json")
PARAM_PATH = os.path.join(BASE_DIR, "model", "active_params.json")

# cek file model
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
if len(sys.argv) != len(feature_names) + 1:  # +1 karena sys.argv[0] adalah script name
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
hasil = "STUNTING" if pred == 1 else "NORMAL"
probabilitas_stunting = float(proba[1])

# =========================
# HITUNG FAKTOR RISIKO SEDERHANA
# =========================
faktor_risiko = {}
for param in feature_names:
    faktor_risiko[param] = round(faktor.get(param, 0), 4)

# =========================
# OUTPUT JSON
# =========================
output = {
    "hasil": hasil,
    "probabilitas": round(probabilitas_stunting, 4),
    "faktor": faktor_risiko
}

print(json.dumps(output))
