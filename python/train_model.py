import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import StratifiedKFold
from sklearn.metrics import accuracy_score
import joblib
import json
import mysql.connector
import os
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
    cursor.execute("""
        SELECT nama_parameter
        FROM parameter
        WHERE status_aktif=1
        ORDER BY id ASC
    """)
    params = cursor.fetchall()
    num_cols = [p['nama_parameter'] for p in params]

    if not num_cols:
        raise ValueError("Tidak ada parameter aktif.")

    # =========================
    # LOAD CSV
    # =========================
    BASE_DIR = os.path.dirname(os.path.abspath(__file__))
    csv_path = os.path.join(BASE_DIR, "dataset_stunting.csv")

    if not os.path.exists(csv_path):
        raise FileNotFoundError(f"CSV dataset tidak ditemukan: {csv_path}")

    csv_data = pd.read_csv(csv_path)

    # Rename kolom jika berbeda
    csv_to_param = {
        "lila": "lingkar_lengan_atas",
        "hb": "kadar_hb"
    }
    csv_data = csv_data.rename(columns=csv_to_param)

    # Pastikan semua parameter aktif ada di CSV
    for col in num_cols:
        if col not in csv_data.columns:
            csv_data[col] = np.nan

    # Clean numeric
    for col in num_cols:
        csv_data[col] = (
            csv_data[col]
            .astype(str)
            .str.replace(r'[^\d\.]', '', regex=True)
        )
        csv_data[col] = pd.to_numeric(csv_data[col], errors='coerce')

    # Target
    if 'stunting' not in csv_data.columns:
        raise ValueError("Kolom 'stunting' tidak ditemukan di CSV.")

    csv_data['stunting'] = csv_data['stunting'].apply(
        lambda x: 1 if str(x).strip().lower() == "stunting" else 0
    )

    csv_data = csv_data[num_cols + ['stunting']]

    # =========================
    # AMBIL DATA DB DINAMIS
    # =========================
    query = """
    SELECT
        ih.id AS ibu_id,
        p.nama_parameter,
        np.nilai
    FROM ibu_hamil ih
    JOIN nilai_parameter np ON np.ibu_id = ih.id
    JOIN parameter p ON p.id = np.parameter_id
    WHERE ih.deleted_at IS NULL
    AND p.status_aktif = 1
    """

    cursor.execute(query)
    rows = cursor.fetchall()

    if rows:
        df_long = pd.DataFrame(rows)

        db_data = df_long.pivot(
            index='ibu_id',
            columns='nama_parameter',
            values='nilai'
        ).reset_index()

        for col in num_cols:
            if col not in db_data.columns:
                db_data[col] = np.nan

        db_data = db_data[['ibu_id'] + num_cols]

        # Target fallback rule-based
        db_data['stunting'] = (
            (db_data.get('tinggi_badan', 999) < 150) |
            (db_data.get('lingkar_lengan_atas', 999) < 23) |
            (db_data.get('kadar_hb', 999) < 11)
        ).astype(int)

        db_data = db_data[num_cols + ['stunting']]
    else:
        db_data = pd.DataFrame(columns=num_cols + ['stunting'])

    # =========================
    # GABUNG CSV + DB
    # =========================
    data = pd.concat([csv_data, db_data], ignore_index=True)

    if data.empty:
        raise ValueError("Dataset kosong.")

    # =========================
    # IMPUTASI MEDIAN (ANTI DEADLOCK)
    # =========================
    imputer_values = {}

    for col in num_cols:
        if data[col].isnull().all():
            median_val = 0
        else:
            median_val = data[col].median()

        data[col] = data[col].fillna(median_val)
        imputer_values[col] = float(median_val)

    if data['stunting'].nunique() < 2:
        raise ValueError("Target hanya 1 kelas. Tambahkan data agar ada 0 dan 1.")

    X = data[num_cols]
    y = data['stunting']

    # =========================
    # CROSS VALIDATION
    # =========================
    kf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    fold_accuracies = []

    for train_idx, test_idx in kf.split(X, y):
        X_train, X_test = X.iloc[train_idx], X.iloc[test_idx]
        y_train, y_test = y.iloc[train_idx], y.iloc[test_idx]

        model = RandomForestClassifier(
            n_estimators=200,
            random_state=42,
            class_weight="balanced"
        )

        model.fit(X_train, y_train)
        y_pred = model.predict(X_test)
        acc = accuracy_score(y_test, y_pred)
        fold_accuracies.append(acc)

    mean_acc = np.mean(fold_accuracies)

    # =========================
    # TRAIN FINAL MODEL
    # =========================
    final_model = RandomForestClassifier(
        n_estimators=200,
        random_state=42,
        class_weight="balanced"
    )

    final_model.fit(X, y)

    # =========================
    # FEATURE IMPORTANCE
    # =========================
    feature_importance = dict(zip(num_cols, final_model.feature_importances_))

    # =========================
    # SIMPAN MODEL & METADATA
    # =========================
    model_dir = os.path.join(BASE_DIR, "model")
    os.makedirs(model_dir, exist_ok=True)

    joblib.dump(final_model, os.path.join(model_dir, "rf_stunting.pkl"))

    with open(os.path.join(model_dir, "feature_importance.json"), "w") as f:
        json.dump(feature_importance, f, indent=4)

    with open(os.path.join(model_dir, "active_params.json"), "w") as f:
        json.dump(num_cols, f, indent=4)

    with open(os.path.join(model_dir, "imputer.json"), "w") as f:
        json.dump(imputer_values, f, indent=4)

    # =========================
    # OUTPUT JSON
    # =========================
    output = {
        "success": True,
        "message": "Training berhasil.",
        "akurasi_cv": round(mean_acc, 2),
        "feature_importance": feature_importance,
        "jumlah_data": len(data)
    }

    print(json.dumps(output))

except Exception as e:
    output = {
        "success": False,
        "message": f"Training gagal: {str(e)}"
    }
    print(json.dumps(output))
    sys.exit(1)
