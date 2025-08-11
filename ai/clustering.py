import pandas as pd
from sklearn.cluster import KMeans

# Step 1: Load the survey data
df = pd.read_csv('survey_data.csv')

# Expecting a 'user_id' column and feature columns (e.g., q1, q2, q3, ...)
if 'user_id' not in df.columns:
    raise ValueError("CSV must have a 'user_id' column")

# Step 2: Select features for clustering (exclude user_id)
features = df.drop(columns=['user_id'])

# Step 3: Apply K-Means clustering
kmeans = KMeans(n_clusters=3, random_state=42)
df['cluster_id'] = kmeans.fit_predict(features)

# Step 4: Keep only user_id and cluster_id
cluster_results = df[['user_id', 'cluster_id']]

# Step 5: Save results to user_clusters.csv
cluster_results.to_csv('user_clusters.csv', index=False)

print("✅ Clustering complete! Results saved to user_clusters.csv")
