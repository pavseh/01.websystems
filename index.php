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

    $name = htmlspecialchars($_POST['name']); // user input
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
</head>
<body>
    <h1>PHP Quiz</h1>
    <form method="post" action="">
        <?php foreach ($questions as $index => $question): ?>
            <fieldset>
                <legend><?php echo $question['question']; ?></legend>
                <?php foreach ($question['options'] as $optionIndex => $option): ?>
                    <label>
                        <input type="radio" name="question<?php echo $index; ?>" value="<?php echo $optionIndex; ?>">
                        <?php echo $option; ?>
                    </label><br>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>
        <input type="submit" value="Submit">
    </form>

    <!-- Show Leaderboard -->
    <h3>Leaderboard:</h3>
    <?php 
        $stmt = $pdo->query("SELECT name, score, total_questions, date_created FROM leaderboard ORDER BY score DESC, date_created ASC LIMIT 10");
        $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($leaderboard) {
            echo "<ul>";
            foreach ($leaderboard as $entry) {
                echo "<li>{$entry['name']} - {$entry['score']}/{$entry['total_questions']} ({$entry['date_created']})</li>";

            }
            echo "</ul>";

        } else {
            echo "<p>No scores yet.</p>";

        }
    ?>

</body>
</html>
