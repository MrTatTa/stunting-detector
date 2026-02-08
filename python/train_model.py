import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import StratifiedKFold
from sklearn.metrics import accuracy_score
import joblib
import json
import mysql.connector
import os
import re
import sys

# =========================
# CONFIG DATABASE
# =========================
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "prediksi_stunting"
}

try:
    # =========================
    # CONNECT DB
    # =========================
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor(dictionary=True)

    # =========================
    # AMBIL PARAMETER AKTIF
    # =========================
    cursor.execute("SELECT nama_parameter FROM parameter WHERE status_aktif=1")
    params = cursor.fetchall()
    num_cols = [p['nama_parameter'] for p in params]

    if not num_cols:
        raise ValueError("Tidak ada parameter aktif di table parameter")

    # =========================
    # LOAD CSV
    # =========================
    BASE_DIR = os.path.dirname(os.path.abspath(__file__))
    csv_path = os.path.join(BASE_DIR, "dataset_stunting.csv")

    if not os.path.exists(csv_path):
        raise FileNotFoundError(f"CSV dataset tidak ditemukan: {csv_path}")

    csv_data = pd.read_csv(csv_path)

    # Mapping nama CSV ke parameter aktif
    csv_to_param = {
        "usia": "usia",
        "tinggi_badan": "tinggi_badan",
        "lila": "lingkar_lengan_atas",
        "hb": "kadar_hb"
    }
    csv_data = csv_data.rename(columns=csv_to_param)

    # =========================
    # CLEAN CSV NUMERIC
    # =========================
    for col in num_cols:
        if col in csv_data.columns:
            csv_data[col] = csv_data[col].astype(str).str.replace(r'[^\d\.]', '', regex=True)
            csv_data[col] = pd.to_numeric(csv_data[col], errors='coerce')

    # =========================
    # MAP TARGET CSV
    # =========================
    csv_data['stunting'] = csv_data['stunting'].apply(lambda x: 1 if str(x).strip().lower() == "stunting" else 0)

    # =========================
    # AMBIL DATA DB
    # =========================
    cursor.execute(f"SELECT id, {', '.join(num_cols)} FROM ibu_hamil")
    db_data = pd.DataFrame(cursor.fetchall())

    for col in num_cols:
        if col in db_data.columns:
            db_data[col] = pd.to_numeric(db_data[col], errors='coerce')

    # Buat target dari DB (aturan tinggi<50/lila<23/hb<11)
    db_data['stunting'] = ((db_data.get('tinggi_badan', 999) < 50) |
                           (db_data.get('lingkar_lengan_atas', 999) < 23) |
                           (db_data.get('kadar_hb', 999) < 11)).astype(int)

    # =========================
    # GABUNG CSV + DB
    # =========================
    data = pd.concat([csv_data[num_cols + ['stunting']], db_data[num_cols + ['stunting']]], ignore_index=True)
    data = data.dropna(subset=num_cols + ['stunting'])

    if data['stunting'].nunique() < 2:
        raise ValueError("Target hanya 1 kelas. Tambahkan data agar ada kelas 0 dan 1.")

    X = data[num_cols]
    y = data['stunting']

    # =========================
    # K-FOLD CROSS VALIDATION
    # =========================
    kf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    fold_accuracies = []

    for train_idx, test_idx in kf.split(X, y):
        X_train, X_test = X.iloc[train_idx], X.iloc[test_idx]
        y_train, y_test = y.iloc[train_idx], y.iloc[test_idx]

        model = RandomForestClassifier(n_estimators=200, random_state=42, class_weight="balanced")
        model.fit(X_train, y_train)
        y_pred = model.predict(X_test)
        acc = accuracy_score(y_test, y_pred)
        fold_accuracies.append(acc)

    mean_acc = np.mean(fold_accuracies)

    # =========================
    # TRAIN FINAL MODEL
    # =========================
    final_model = RandomForestClassifier(n_estimators=200, random_state=42, class_weight="balanced")
    final_model.fit(X, y)

    # =========================
    # FEATURE IMPORTANCE
    # =========================
    feature_importance = dict(zip(num_cols, final_model.feature_importances_))

    # =========================
    # SIMPAN MODEL
    # =========================
    os.makedirs(os.path.join(BASE_DIR, "model"), exist_ok=True)
    joblib.dump(final_model, os.path.join(BASE_DIR, "model", "rf_stunting.pkl"))

    with open(os.path.join(BASE_DIR, "model", "feature_importance.json"), "w") as f:
        json.dump(feature_importance, f, indent=4)

    with open(os.path.join(BASE_DIR, "model", "active_params.json"), "w") as f:
        json.dump(num_cols, f, indent=4)

    # =========================
    # OUTPUT JSON ONLY
    # =========================
    output = {
        "success": True,
        "message": "Model, feature importance, dan parameter aktif tersimpan.",
        "akurasi_cv": round(mean_acc, 2),
        "feature_importance": feature_importance
    }

    print(json.dumps(output))

except Exception as e:
    # Jika ada error, kirim JSON juga
    output = {
        "success": False,
        "message": f"Training gagal: {str(e)}"
    }
    print(json.dumps(output))
    sys.exit(1)
