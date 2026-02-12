import sys
import json
import joblib
import pandas as pd
import os

# =========================
# SET BASE PATH
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_PATH = os.path.join(BASE_DIR, "model", "rf_stunting.pkl")
FEATURE_PATH = os.path.join(BASE_DIR, "model", "feature_importance.json")
PARAM_PATH = os.path.join(BASE_DIR, "model", "active_params.json")
IMPUTER_PATH = os.path.join(BASE_DIR, "model", "imputer.json")

try:
    # =========================
    # VALIDASI FILE
    # =========================
    if not os.path.exists(MODEL_PATH):
        raise FileNotFoundError("Model belum dilatih.")

    if not os.path.exists(PARAM_PATH):
        raise FileNotFoundError("File active_params.json tidak ditemukan.")

    if not os.path.exists(FEATURE_PATH):
        raise FileNotFoundError("File feature_importance.json tidak ditemukan.")

    if not os.path.exists(IMPUTER_PATH):
        raise FileNotFoundError("File imputer.json tidak ditemukan. Silakan retrain.")

    # =========================
    # LOAD MODEL & METADATA
    # =========================
    model = joblib.load(MODEL_PATH)

    with open(PARAM_PATH) as f:
        feature_names = json.load(f)

    with open(FEATURE_PATH) as f:
        feature_importance = json.load(f)

    with open(IMPUTER_PATH) as f:
        imputer_values = json.load(f)

    if not feature_names:
        raise ValueError("Daftar parameter aktif kosong.")

    # =========================
    # VALIDASI ARGUMEN INPUT
    # =========================
    expected_param_count = len(feature_names)
    received_param_count = len(sys.argv) - 1

    if received_param_count != expected_param_count:
        raise ValueError(
            f"Jumlah parameter salah. Dibutuhkan {expected_param_count}, "
            f"diterima {received_param_count}. "
            f"Format: {' '.join(feature_names)}"
        )

    # =========================
    # KONVERSI KE FLOAT
    # =========================
    try:
        values = [float(x) if x.strip() != "" else None for x in sys.argv[1:]]
    except ValueError:
        raise ValueError("Semua parameter harus berupa angka.")

    # =========================
    # BUAT DATAFRAME SESUAI TRAINING
    # =========================
    X = pd.DataFrame([values], columns=feature_names)

    # =========================
    # IMPUTASI (PAKAI MEDIAN TRAINING)
    # =========================
    for col in feature_names:
        if X[col].isnull().any():
            X[col] = X[col].fillna(imputer_values.get(col, 0))

    # Pastikan urutan kolom sama persis
    X = X[feature_names]

    # =========================
    # PREDIKSI
    # =========================
    pred = model.predict(X)[0]
    proba = model.predict_proba(X)[0]

    hasil = "STUNTING" if pred == 1 else "NORMAL"
    probabilitas_stunting = float(proba[1])

    # =========================
    # FAKTOR RISIKO
    # =========================
    faktor_risiko = {}

    for param in feature_names:
        faktor_risiko[param] = round(
            float(feature_importance.get(param, 0)), 4
        )

    # =========================
    # OUTPUT JSON
    # =========================
    output = {
        "success": True,
        "hasil": hasil,
        "probabilitas": round(probabilitas_stunting, 4),
        "faktor": faktor_risiko,
        "fitur_digunakan": feature_names
    }

    print(json.dumps(output))

except Exception as e:
    error_output = {
        "success": False,
        "error": str(e)
    }
    print(json.dumps(error_output))
    sys.exit(1)
