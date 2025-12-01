<?php
    require_once '../includes/db.php';

    $sql = "SELECT DATE_FORMAT(datum, '%M') AS mesec, COUNT(*) AS broj 
            FROM korpa 
            GROUP BY MONTH(datum)
            ORDER BY MONTH(datum)";
    $result = $conn->query($sql);


    $labelsMeseci = [];
    $dataMeseci = [];

    while ($row = $result->fetch_assoc()) {
        $labelsMeseci[] = $row['mesec'];
        $dataMeseci[] = $row['broj'];
    }

    $queryAutori = "SELECT autor, COUNT(*) AS broj_knjiga FROM proizvod GROUP BY autor";
    $resultAutori = $conn->query($queryAutori);

    $autori = [];
    $brojKnjiga = [];

    while ($row = $resultAutori->fetch_assoc()) {
        $autori[] = $row['autor'];
        $brojKnjiga[] = $row['broj_knjiga'];
    }

    $sqlKategorije = "
    SELECT k.naziv AS kategorija, COUNT(p.prozivod_id) AS broj_proizvoda
    FROM kategorija k
    LEFT JOIN proizvod p ON k.kategorija_id = p.kategorija_id
    GROUP BY k.kategorija_id, k.naziv
    ORDER BY broj_proizvoda DESC
    ";
    $resultKategorije = $conn->query($sqlKategorije);
    
    $kategorije = [];
    $kategorijeValues = [];
    
    if($resultKategorije && $resultKategorije->num_rows > 0){
        while ($row = $resultKategorije->fetch_assoc()) {
            $kategorije[] = $row['kategorija'];
            $kategorijeValues[] = (int)$row['broj_proizvoda']; 
        }
    }


?>
<!DOCTYPE html>
    <html lang="sr">
        <head>
    <meta charset="UTF-8">
    <title>Statistika prikaz</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- OVA LINIJA TI TREBA -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
</head>

        <body style="max-width:1000px;margin:auto;">
            <h2 style="text-align:center;">Broj dodatih knjiga u korpu po mesecima</h2>
            <canvas id="korpaChart"></canvas>

            <h2 style="text-align:center;">Broj knjiga po Autoru</h2>
            <canvas id="autorChart"></canvas>

            <h2 style="text-align:center;">Broj proizvoda po kategorijama</h2>
            <canvas id="pieChart"></canvas>

            <script>
                const labelsMeseci = <?php echo json_encode($labelsMeseci); ?>;
                const dataMeseci = <?php echo json_encode($dataMeseci); ?>;

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
                

                new Chart(document.getElementById('pieChart'), { 
    type: 'pie',
    data: {
        labels: <?php echo json_encode($kategorije); ?>,
        datasets: [{
            data: <?php echo json_encode($kategorijeValues); ?>,
            backgroundColor: [
                '#ff6384', '#36a2eb', '#ffce56',
                '#4bc0c0', '#9966ff', '#ff9f40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            datalabels: {
                color: '#fff',
                font: {
                    weight: 'bold',
                    size: 14
                },
                formatter: (value, ctx) => {
                    let total = ctx.chart.data.datasets[0].data
                        .reduce((a, b) => a + b, 0);
                    let percentage = (value / total * 100).toFixed(1);
                    return percentage + '%';
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});
            </script>
        </body>
    </html>