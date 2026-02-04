import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import StratifiedKFold
from sklearn.metrics import accuracy_score
import joblib
import os
import json
import numpy as np

# =========================
# LOAD DATA
# =========================
data = pd.read_csv("dataset_stunting.csv")

print("Kolom CSV:")
print(data.columns.tolist())

# =========================
# PILIH KOLOM (IBU HAMIL)
# =========================
data = data[['usia', 'tinggi_badan', 'lila', 'hb', 'stunting']]

# =========================
# BERSIHKAN NUMERIK
# =========================
num_cols = ['usia', 'tinggi_badan', 'lila', 'hb']

for col in num_cols:
    data[col] = (
        data[col]
        .astype(str)
        .str.replace(',', '.', regex=False)
        .str.extract(r'([0-9]+\.?[0-9]*)')
        .astype(float)
    )

# =========================
# BERSIHKAN TARGET
# =========================
data['stunting'] = (
    data['stunting']
    .fillna("")
    .astype(str)
    .str.lower()
    .str.strip()
    .apply(lambda x: 1 if 'stunting' in x else 0)
)

# =========================
# DROP NA (AMAN)
# =========================
data = data.dropna(subset=num_cols + ['stunting'])

print("\nJumlah data siap training:", len(data))
print("\nDistribusi target:")
print(data['stunting'].value_counts())

if data['stunting'].nunique() < 2:
    raise ValueError("❌ Target hanya 1 kelas. Dataset tidak seimbang.")

# =========================
# SIAPKAN DATA
# =========================
X = data[num_cols]
y = data['stunting']

# =========================
# K-FOLD CV
# =========================
kf = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
fold_accuracies = []

for i, (train_index, test_index) in enumerate(kf.split(X, y), 1):
    X_train, X_test = X.iloc[train_index], X.iloc[test_index]
    y_train, y_test = y.iloc[train_index], y.iloc[test_index]

    model = RandomForestClassifier(
        n_estimators=200,
        random_state=42,
        class_weight="balanced"
    )

    model.fit(X_train, y_train)
    y_pred = model.predict(X_test)

    acc = accuracy_score(y_test, y_pred)
    fold_accuracies.append(acc)

    print(f"Fold {i} - Akurasi: {acc:.2f}")

# =========================
# RATA-RATA AKURASI
# =========================
mean_acc = np.mean(fold_accuracies)
print(f"\n⭐ Akurasi rata-rata 5-fold CV: {mean_acc:.2f}")

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

print("\n✅ Model disimpan: model/rf_stunting.pkl")
print("✅ Feature importance disimpan: model/feature_importance.json")
