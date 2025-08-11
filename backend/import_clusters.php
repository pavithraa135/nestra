<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['clusters_file'])) {
    $file = $_FILES['clusters_file']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== false) {
        // Skip header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $user_id = intval($data[0]);
            $cluster_id = intval($data[1]);

            $stmt = $conn->prepare("REPLACE INTO user_clusters (user_id, cluster_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $cluster_id);
            $stmt->execute();
            $stmt->close();
        }

        fclose($handle);
        echo "✅ Clusters imported successfully.";
    } else {
        echo "❌ Error reading uploaded file.";
    }
} else {
    echo "Upload a CSV file with columns: user_id, cluster_id.";
}

$conn->close();
?>
