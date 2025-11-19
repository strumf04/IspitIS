<?php
    require_once '../includes/db.php';

    $sql = "SELECT DATE_FORMAT(datum, '%M') AS mesec, COUNT(*) AS broj 
            FROM korpa 
            GROUP BY MONTH(datum)
            ORDER BY MONTH(datum)";
    $result = $conn->query($sql);


    $labels = [];
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['mesec'];
        $data[] = $row['broj'];
    }

    $queryAutori = "SELECT autor, COUNT(*) AS broj_knjiga FROM proizvod GROUP BY autor";
    $resultAutori = $conn->query($queryAutori);

    $autori = [];
    $brojKnjiga = [];

    while ($row = $resultAutori->fetch_assoc()) {
        $autori[] = $row['autor'];
        $brojKnjiga[] = $row['broj_knjiga'];
    }


?>
<!DOCTYPE html>
    <html lang="sr">
        <head>
            <meta charset="UTF-8">
            <title>Statistika prikaz</title>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>
        <body style="max-width:1000px;margin:auto;">
            <h2 style="text-align:center;">Broj dodatih knjiga u korpu po mesecima</h2>
            <canvas id="korpaChart"></canvas>

            <h2 style="text-align:center;">Broj knjiga po Autoru</h2>
            <canvas id="autorChart"></canvas>

            <script>
                const labelsMeseci = <?php echo json_encode($labels); ?>;
                const dataMeseci = <?php echo json_encode($data); ?>;

                new Chart(document.getElementById("korpaChart"), {
                    type: "line",
                    data: {
                    labels: labelsMeseci,
                    datasets: [{
                        label: "Broj dodatih knjiga",
                        data: dataMeseci,
                        borderColor: "rgba(75, 192, 192, 1)",
                        backgroundColor: "rgba(75, 192, 192, 0.2)",
                        tension: 0
                    }]
                    },
                    options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                    }
                });

                const labelsAutori = <?php echo json_encode($autori); ?>;
                const dataAutori = <?php echo json_encode($brojKnjiga); ?>;

                new Chart(document.getElementById("autorChart"), {
                    type: "bar",
                    data: {
                    labels: labelsAutori,
                    datasets: [{
                        label: "Broj knjiga od",
                        data: dataAutori,
                        backgroundColor: "rgba(153, 102, 255, 0.6)",
                        borderColor: "rgba(153, 102, 255, 1)",
                        borderWidth: 1
                    }]
                    },
                    options: {
                    scales: {
                        y: { beginAtZero: true }
                    }
                    }
                });
            </script>
        </body>
    </html>