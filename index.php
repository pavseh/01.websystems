<!-- Ivern Bryant C. Buala -->
<!-- Web Systems -->

<!-- PHP part -->
<?php

$host = 'localhost:3307';
$dbname = 'quizdb';
$username = 'root';
$password = '';

// Connecting to Database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Database connected successfully!";

    } catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}



// Define questions and answers
$questions = [
    [
        "question" => "What does PHP stand for?",
        "options" => ["Personal Home Page", "Private Home Page", "PHP: Hypertext Preprocessor", "Public Hypertext Preprocessor"],
        "answer" => 2
    ],
    [
        "question" => "Which symbol is used to access a property of an object in PHP?",
        "options" => [".", "->", "::", "#"],
        "answer" => 1
    ],
    [
        "question" => "Which function is used to include a file in PHP?",
        "options" => ["include()", "require()", "import()", "load()"],
        "answer" => 0
    ]
];


// Initialize score
$score = 0;


// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($questions as $index => $question) {
        if (isset($_POST["question$index"]) && $_POST["question$index"] == $question['answer']) {
            $score++;
        }
    }


    // Input Name, Score, and shows total questions answered
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : 'Anonymous';
    $stmt = $pdo->prepare("INSERT INTO leaderboard (name, score, total_questions) VALUES (:name, :score, :total_questions)");
    $stmt->execute([
        ':name' => $name,
        ':score' => $score,
        ':total_questions' => count($questions)
    ]);


    // Display Score
    echo "<h2>Your Score: $score/" . count($questions) . "</h2>";
    echo '<a href="index.php">Try Again</a>';

    // Display Leaderboard
    echo "<h3>Leaderboard:</h3>";
    $stmt = $pdo->query("SELECT name, score, total_questions, date_created FROM leaderboard ORDER BY score DESC, date_created ASC LIMIT 10");
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if($leaderboard) {
        echo "<ul>";
        foreach ($leaderboard as $entry) {
            echo "<li>{$entry['name']} - {$entry['score']}/{$entry['total_questions']} ({$entry['date_created']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No scores yet.</p>";
    }
    exit;
}
?>



<!-- html part -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Quiz</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h1 class=" text-center mb-4 bg-dark text-white rounded">PHP Quiz Form</h1>

        <form method="post" action="" class="shadow-lg p-4 bg-white rounded">
            
            <!-- Input Name section -->
            <div class="mb-3">
                <label for="name" class="form-label">Your Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <?php foreach ($questions as $index => $question): ?>
                <fieldset class="mb-4">
                    <legend class="fw-bold"><?php echo $question['question']; ?></legend>
                    <?php foreach ($question['options'] as $optionIndex => $option): ?>
                        <div class="form-check">
                            <input type="radio" name="question<?php echo $index; ?>" value="<?php echo $optionIndex; ?>" class="form-check-input" id="option<?php echo $index; ?>_<?php echo $optionIndex; ?>">
                            <label class="form-check-label" for="option<?php echo $index; ?>_<?php echo $optionIndex; ?>">
                                <?php echo $option; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-dark w-10">Submit Quiz</button>
        </form>

        <!-- Leaderboard section -->
        <div class="mt-5">
            <h3>Leaderboard:</h3>
            <?php 
                $stmt = $pdo->query("SELECT name, score, total_questions, date_created FROM leaderboard ORDER BY score DESC, date_created ASC LIMIT 10");
                $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($leaderboard) {
                    echo "<ul class='list-group'>";
                    foreach ($leaderboard as $entry) {
                        echo "<li class='list-group-item'>{$entry['name']} - {$entry['score']}/{$entry['total_questions']} ({$entry['date_created']})</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No scores yet.</p>";
                }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>