"""
Nestra AI Matching Engine – Python (KMeans + Cosine Similarity)
==================================================================
Usage:
    python match.py                  → runs full pipeline, outputs user_clusters.csv + matches.json
    python match.py --user 4         → prints top 5 matches for user_id 4

Pipeline:
    1. Load survey_data.csv  (exported from PHP via export_survey.php)
    2. Encode categorical columns with weighted one-hot encoding
    3. K-Means cluster users into groups
    4. Within each cluster, compute cosine similarity for precise ranking
    5. Output: user_clusters.csv + matches.json

Weights (must match match_engine.php):
    sleep=20, cleanliness=18, noise=15, social=14, diet=12, room=10, pets=6, work=5
"""

import argparse
import json
import sys
from pathlib import Path

import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from sklearn.preprocessing import OneHotEncoder
from sklearn.metrics.pairwise import cosine_similarity

# ── Config ──────────────────────────────────────────────────────────────────
DATA_FILE     = Path(__file__).parent / "survey_data.csv"
CLUSTERS_OUT  = Path(__file__).parent / "user_clusters.csv"
MATCHES_OUT   = Path(__file__).parent / "matches.json"
N_CLUSTERS    = 3   # Increase as user base grows (rule of thumb: sqrt(n_users/2))

# Feature weights (higher = more important in compatibility)
FEATURE_WEIGHTS = {
    "sleep":        20,
    "cleanliness":  18,
    "noise":        15,
    "social":       14,
    "diet":         12,
    "room":         10,
    "pets":          6,
    "work":          5,
}

FEATURE_COLS = list(FEATURE_WEIGHTS.keys())

# ── Load & Validate ──────────────────────────────────────────────────────────
def load_data():
    if not DATA_FILE.exists():
        print(f"❌ {DATA_FILE} not found. Run export_survey.php first.")
        sys.exit(1)

    df = pd.read_csv(DATA_FILE)
    required = {"user_id"} | set(FEATURE_COLS)
    missing  = required - set(df.columns)
    if missing:
        print(f"❌ Missing columns: {missing}")
        sys.exit(1)

    # Keep only latest survey per user
    if "submitted_at" in df.columns:
        df = df.sort_values("submitted_at").drop_duplicates("user_id", keep="last")

    df = df.dropna(subset=FEATURE_COLS)
    print(f"✅ Loaded {len(df)} users from survey data.")
    return df.reset_index(drop=True)


# ── Encode with Weights ──────────────────────────────────────────────────────
def encode_features(df):
    enc = OneHotEncoder(sparse_output=False, handle_unknown="ignore")
    encoded = enc.fit_transform(df[FEATURE_COLS])

    # Apply column-level weights
    feature_names = enc.get_feature_names_out(FEATURE_COLS)
    weight_vector = np.array([
        FEATURE_WEIGHTS.get(name.split("_")[0], 1)
        for name in feature_names
    ], dtype=float)

    # Normalize weights so they sum to 1
    weight_vector /= weight_vector.sum()
    weighted = encoded * weight_vector

    return weighted, enc


# ── K-Means Clustering ───────────────────────────────────────────────────────
def cluster_users(df, weighted_features):
    n = len(df)
    k = min(N_CLUSTERS, max(2, n // 2))  # Need at least 2 per cluster

    km = KMeans(n_clusters=k, random_state=42, n_init=10)
    df["cluster_id"] = km.fit_predict(weighted_features)
    print(f"✅ Clustered {n} users into {k} groups.")
    print(df.groupby("cluster_id").size().to_string())
    return df


# ── Cosine Similarity Matching ───────────────────────────────────────────────
def compute_matches(df, weighted_features):
    sim_matrix = cosine_similarity(weighted_features)
    all_matches = {}

    for i, row in df.iterrows():
        uid   = int(row["user_id"])
        scores = []
        same_cluster = df[df["cluster_id"] == row["cluster_id"]]

        for j, other in same_cluster.iterrows():
            if j == i:
                continue
            other_uid = int(other["user_id"])
            sim       = float(sim_matrix[i][j])
            compat    = round(sim * 100, 1)
            scores.append({
                "user_id":       other_uid,
                "fullname":      str(other.get("fullname", f"User {other_uid}")),
                "compatibility": compat,
            })

        scores.sort(key=lambda x: x["compatibility"], reverse=True)
        all_matches[uid] = scores[:5]

    return all_matches


# ── Main ─────────────────────────────────────────────────────────────────────
def main():
    parser = argparse.ArgumentParser(description="Nestra AI Matching Engine")
    parser.add_argument("--user", type=int, help="Print top 5 matches for a specific user_id")
    args = parser.parse_args()

    df = load_data()

    if len(df) < 2:
        print("⚠️  Need at least 2 users to generate matches.")
        sys.exit(0)

    weighted_features, _ = encode_features(df)
    df = cluster_users(df, weighted_features)

    # Save clusters
    df[["user_id", "cluster_id"]].to_csv(CLUSTERS_OUT, index=False)
    print(f"✅ Clusters saved → {CLUSTERS_OUT}")

    # Compute matches
    matches = compute_matches(df, weighted_features)

    # Save all matches
    with open(MATCHES_OUT, "w") as f:
        json.dump(matches, f, indent=2)
    print(f"✅ Matches saved → {MATCHES_OUT}")

    # If --user flag given, print that user's matches
    if args.user:
        uid   = args.user
        user_matches = matches.get(uid, [])
        if not user_matches:
            print(f"\n⚠️  No matches found for user_id {uid}.")
        else:
            print(f"\n🏆 Top matches for user_id {uid}:")
            for rank, m in enumerate(user_matches, 1):
                print(f"  {rank}. {m['fullname']} (user {m['user_id']}) — {m['compatibility']}% compatible")
    else:
        print(f"\n📊 Summary: {len(df)} users, {len(matches)} match profiles generated.")


if __name__ == "__main__":
    main()
