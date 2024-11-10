<?php
// Inclure la configuration de la base de données
include 'config.php';

// Vérifier la connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage();
    exit;
}

// Charger les messages depuis la base de données
$stmt = $pdo->query("SELECT * FROM messages ORDER BY id DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
   
</head>
<body>

<h1>Chat Messenger</h1>

<!-- Formulaire d'envoi de message -->
<form action="index.php" method="POST">
    <input type="text" name="pseudo" placeholder="Entrez votre pseudo" required><br>
    <input type="text" name="message" placeholder="Votre message" required><br>
    <input type="submit" value="Envoyer">
</form>

<!-- Afficher les messages -->
<div class="chat-box">
    <h2>Messages</h2>
    <?php foreach ($messages as $message): ?>
        <div class="message">
            <strong><?php echo htmlspecialchars($message['pseudo']); ?>:</strong>
            <?php echo htmlspecialchars($message['message']); ?>
        </div>
    <?php endforeach; ?>
</div>

<?php
// Si un message est envoyé, l'ajouter à la base de données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO messages (pseudo, message) VALUES (?, ?)");
    $stmt->execute([$pseudo, $message]);

    // Rediriger pour éviter que le message soit envoyé plusieurs fois lors du rechargement de la page
    header("Location: index.php");
    exit();
}
?>


   
    <style>
        /* Styles de base */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('https://images3.alphacoders.com/748/thumb-1920-748367.jpg');
            background-size: cover;
            background-attachment: fixed;
            color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            animation: backgroundAnimation 30s ease-in-out infinite;
        }

        @keyframes backgroundAnimation {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        /* Conteneur principal */
        .container {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #ffeb3b;
            font-size: 2em;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        /* Formulaires */
        form {
            margin: 20px 0;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"],
        input[type="submit"],
        button {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            font-size: 1em;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 80%;
        }

        input[type="submit"], button {
            background: linear-gradient(45deg, #ff0057, #ff7e00);
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover,
        button:hover {
            background: linear-gradient(45deg, #ff7e00, #ff0057);
        }

        /* Sections pour musique et recherche YouTube */
        .song-choice label,
        .video-results p {
            font-size: 1.1em;
            color: #ffffff;
        }

        .song-choice,
        .youtube-search {
            margin-top: 20px;
        }

        /* Liens externes */
        .external-links a {
            margin: 10px;
            padding: 10px 15px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .external-links a:hover {
            background-color: #2563eb;
        }
    </style>
    <!-- Recherche YouTube -->
    <div class="youtube-search">
        <h2>Recherche YouTube</h2>
        <form id="youtube-search-form" action="#" method="POST">
            <input type="text" id="search-query" placeholder="Recherchez sur YouTube..." required>
            <input type="submit" value="Rechercher">
        </form>
        <div class="video-results" id="video-results"></div>
    </div>

    <script>
        const apiKeyYouTube = "AIzaSyBnsGkf2D_vWBpW14wwIQ9HROK8_Rs1YXg";

        document.getElementById('youtube-search-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const query = document.getElementById('search-query').value;
            searchYouTube(query);
        });

        function searchYouTube(query) {
            const endpoint = `https://www.googleapis.com/youtube/v3/search?part=snippet&maxResults=7&q=${query}&type=video&key=${apiKeyYouTube}`;
            fetch(endpoint)
                .then(response => response.json())
                .then(data => {
                    const videoResults = document.getElementById('video-results');
                    videoResults.innerHTML = '';
                    data.items.forEach(item => {
                        const videoId = item.id.videoId;
                        const videoTitle = item.snippet.title;
                        const videoContainer = document.createElement('div');
                        videoContainer.innerHTML = `
                            <iframe width="300" height="170" src="https://www.youtube.com/embed/${videoId}" allowfullscreen></iframe>
                            <p>${videoTitle}</p>
                        `;
                        videoResults.appendChild(videoContainer);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }
    </script>
</head>
<body>
    <h1>Welcome to the Chat Room</h1>
    <!-- Form to enter username -->
    <form method="post">
        <label for="username">Enter Username:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit">Join</button>
    </form>

    <div id="chat">
        <!-- Display chat messages here -->
    </div>

    <!-- Form to send messages -->
    <form id="messageForm">
        <input type="text" id="messageInput" placeholder="Enter your message" required>
        <button type="submit">Send</button>
    </form>

    <script>
        // JavaScript to handle message submission and automatic refresh
        document.getElementById('messageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = document.getElementById('messageInput').value;
            const username = "<?php echo $_SESSION['username'] ?? ''; ?>";
            if (username) {
                await fetch('loadMessages.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, message })
                });
                document.getElementById('messageInput').value = '';
            } else {
                alert('Please register a username first.');
            }
        });

        // JavaScript to refresh messages every few seconds
        setInterval(async () => {
            const response = await fetch('loadMessages.php');
            const messages = await response.text();
            document.getElementById('chat').innerHTML = messages;
        }, 2000);

        // Free the username when the window is closed
        window.addEventListener('beforeunload', () => {
            fetch('removeUser.php'); // New script for removing user
        });
    </script>
</body>
</html>
