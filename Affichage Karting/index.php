<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projet Karting</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Inclure la bibliothèque Leaflet.js -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    /* Style pour la carte */
    #mapid { height: 400px; }
    .kart-marker {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: 2px solid #fff;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="left-column">
      <h2>Classement en temps réel de la course</h2>
      <?php
        include 'db_connexion.php';

        $sql = "SELECT k.KartID, k.LapTime, k.ToursPiste, CONCAT('Kart ', k.KartID, ' - ', p.Nom, ' ', p.Prenom) AS Pilote, pt.PositionFinale
                FROM karts k
                JOIN participation pt ON k.KartID = pt.KartID
                JOIN pilotes p ON pt.PiloteID = p.PiloteID
                JOIN courses c ON pt.CourseID = c.CourseID
                WHERE pt.Statut = 'En cours'
                ORDER BY pt.PositionFinale ASC";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          echo "<ol>";
          while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row["Pilote"] . " - Position : " . $row["PositionFinale"] . "</li>";
          }
          echo "</ol>";
        } else {
          echo "Aucune donnée trouvée dans la base de données.";
        }

        $conn->close();
      ?>
    </div>
    <div class="right-column">
      <h2>Carte du circuit</h2>
      <!-- Conteneur pour afficher la carte -->
      <div id="mapid"></div>
    </div>
  </div>

  <script>
    // Initialiser la carte Leaflet
    var map = L.map('mapid').setView([45.717232, 4.882623], 15); // Centrez la carte sur les premières coordonnées et définissez le niveau de zoom

    // Ajouter une couche de tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Tableau des coordonnées du circuit
    var circuit = [
      [45.717232, 4.882623], // Départ
      [45.717386, 4.882389],
      [45.717515, 4.882159],
      [45.71725, 4.881582],
      [45.717188, 4.881464],
      [45.717015, 4.881218],
      [45.71691, 4.88102],
      [45.716805, 4.880891],
      [45.716727, 4.88073],
      [45.716517, 4.880435],
      [45.716315, 4.880124],
      [45.715985, 4.87963],
      [45.715637, 4.880096],
      [45.715416, 4.880327],
      [45.715255, 4.880601],
      [45.715382, 4.880837],
      [45.715562, 4.881105],
      [45.715671, 4.881271],
      [45.715764, 4.881405],
      [45.715869, 4.881566],
      [45.715989, 4.881706],
      [45.71612, 4.882081],
      [45.71636, 4.882409],
      [45.716675, 4.88272],
      [45.716787, 4.882848],
      [45.716956, 4.882972],
      [45.717087, 4.882816]
    ];

    // Tracer le circuit sur la carte
    L.polyline(circuit, {color: 'red'}).addTo(map);

    // Tableau des couleurs pour les karts
    var kartColors = ['blue', 'green', 'red', 'orange', 'purple', 'yellow'];

    // Initialiser les marqueurs pour les 6 karts
    var kartMarkers = [];
    var kartPositions = [];
    for (var i = 0; i < 6; i++) {
      var colorIndex = i % kartColors.length; // Assurer que chaque couleur est différente
      var color = kartColors[colorIndex];
      // Sélectionner une position aléatoire sur la piste
      var randomIndex = Math.floor(Math.random() * circuit.length);
      var latLng = circuit[randomIndex];
      kartPositions.push(randomIndex);
      var marker = L.marker(latLng, {
        icon: L.divIcon({
          className: 'kart-marker',
          html: `<div style="background-color: ${color}" class="marker-icon"></div>`
        })
      }).addTo(map);
      kartMarkers.push(marker);
    }

    // Fonction pour mettre à jour les positions des karts toutes les 2 secondes
    function updateKartPositions() {
      for (var i = 0; i < 6; i++) {
        // Avancer à la prochaine position
        kartPositions[i] = (kartPositions[i] + 1) % circuit.length;
        var newPosition = circuit[kartPositions[i]];
        kartMarkers[i].setLatLng(newPosition);
      }
    }

    // Mettre à jour les positions des karts toutes les 2 secondes
    setInterval(updateKartPositions, 2000);
  </script>
</body>
</html>
