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

# =========================
# CONFIG DATABASE
# =========================
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "prediksi_stunting"
}

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
    raise ValueError("❌ Tidak ada parameter aktif di table parameter")

print("Parameter aktif:", num_cols)

# =========================
# LOAD CSV
# =========================
csv_path = "dataset_stunting.csv"
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
        # hapus teks cm, tahun, th, dsb
        csv_data[col] = csv_data[col].astype(str).str.replace(r'[^\d\.]', '', regex=True)
        csv_data[col] = pd.to_numeric(csv_data[col], errors='coerce')

# =========================
# MAP TARGET
# =========================
# stunting = 1, Baik = 0
csv_data['stunting'] = csv_data['stunting'].apply(lambda x: 1 if str(x).strip().lower() == "stunting" else 0)

# =========================
# AMBIL DATA DB
# =========================
if set(num_cols).issubset({"usia","tinggi_badan","lingkar_lengan_atas","kadar_hb"}):
    cols = ", ".join(num_cols + ["id"])
else:
    cols = ", ".join(num_cols + ["id"])

cursor.execute(f"SELECT id, {', '.join(num_cols)} FROM ibu_hamil")
db_data = pd.DataFrame(cursor.fetchall())

# =========================
# CLEAN DB NUMERIC
# =========================
for col in num_cols:
    if col in db_data.columns:
        db_data[col] = pd.to_numeric(db_data[col], errors='coerce')

# =========================
# TARGET DARI DB
# =========================
# Jika DB tidak punya kolom target, kita buat aturan:
# stunting jika tinggi < 50 OR lila < 23 OR hb < 11
db_data['stunting'] = ((db_data.get('tinggi_badan', 999) < 50) |
                       (db_data.get('lingkar_lengan_atas', 999) < 23) |
                       (db_data.get('kadar_hb', 999) < 11)).astype(int)

# =========================
# GABUNG CSV + DB
# =========================
data = pd.concat([csv_data[num_cols + ['stunting']], db_data[num_cols + ['stunting']]], ignore_index=True)

# DROP NA
data = data.dropna(subset=num_cols + ['stunting'])

print(f"Jumlah data siap training: {len(data)}")
print("Distribusi target:")
print(data['stunting'].value_counts())

if data['stunting'].nunique() < 2:
    raise ValueError("❌ Target hanya 1 kelas. Tambahkan data agar ada kelas 0 dan 1.")

# =========================
# X dan y
# =========================
X = data[num_cols]
y = data['stunting']

# =========================
# K-FOLD CROSS VALIDATION
# =========================
kf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
fold_accuracies = []

for i, (train_idx, test_idx) in enumerate(kf.split(X, y), 1):
    X_train, X_test = X.iloc[train_idx], X.iloc[test_idx]
    y_train, y_test = y.iloc[train_idx], y.iloc[test_idx]

    model = RandomForestClassifier(n_estimators=200, random_state=42, class_weight="balanced")
    model.fit(X_train, y_train)
    y_pred = model.predict(X_test)
    acc = accuracy_score(y_test, y_pred)
    fold_accuracies.append(acc)
    print(f"Fold {i} - Akurasi: {acc:.2f}")

mean_acc = np.mean(fold_accuracies)
print(f"⭐ Akurasi rata-rata 5-fold CV: {mean_acc:.2f}")

# =========================
# TRAIN FINAL MODEL
# =========================
final_model = RandomForestClassifier(n_estimators=200, random_state=42, class_weight="balanced")
final_model.fit(X, y)

# =========================
# FEATURE IMPORTANCE
# =========================
feature_importance = dict(zip(num_cols, final_model.feature_importances_))
print("\nFeature importances:")
for col, imp in feature_importance.items():
    print(f"{col} : {imp:.4f}")

# =========================
# SIMPAN MODEL
# =========================
os.makedirs("model", exist_ok=True)
joblib.dump(final_model, "model/rf_stunting.pkl")

with open("model/feature_importance.json", "w") as f:
    json.dump(feature_importance, f, indent=4)

with open("model/active_params.json", "w") as f:
    json.dump(num_cols, f, indent=4)

print("\n✅ Model, feature importance, dan parameter aktif tersimpan.")
