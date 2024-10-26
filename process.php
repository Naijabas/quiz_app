<?php
$host = "localhost";
$dbname = "quiz_app";
$username = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['questions'])) {
    $input = trim($_POST['questions']);

    // Split the questions
    $questionsArray = preg_split("/\n\s*\n/", $input);
    $questionsArray = array_filter($questionsArray);

    $totalQuestions = count($questionsArray);
    $totalInserted = 0;
    $duplicates = 0;
    $invalidFormatCount = 0;
    $existingQuestions = [];

    foreach ($questionsArray as $q) {
        $lines = explode("\n", trim($q));
        if (count($lines) < 3) {
            $invalidFormatCount++;
            continue;
        }

        $questionText = $lines[0];
        $correctAnswerLine = array_pop($lines);
        preg_match('/Correct answer: (.+)/i', $correctAnswerLine, $correctMatch);

        if (!$correctMatch) {
            $invalidFormatCount++;
            continue;
        }

        $correctAnswer = trim($correctMatch[1]);
        $answerOptions = array_slice($lines, 1);

        try {
            $pdo->beginTransaction();

            // Check for duplicate question
            $stmt = $pdo->prepare("INSERT INTO questions (question) VALUES (?)");
            $stmt->execute([$questionText]);
            $questionId = $pdo->lastInsertId();

            foreach ($answerOptions as $option) {
                if (preg_match('/^\((.)\) (.+)$/i', $option, $optMatch)) {
                    $answerOption = $optMatch[2];
                    $isCorrect = (strtolower(trim($answerOption)) === strtolower($correctAnswer)) ? 1 : 0;

                    $stmt = $pdo->prepare("INSERT INTO answers (question_id, answer_option, is_correct) VALUES (?, ?, ?)");
                    $stmt->execute([$questionId, $answerOption, $isCorrect]);
                }
            }

            $pdo->commit();
            $totalInserted++;
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') { // Duplicate entry
                $duplicates++;
                $existingQuestions[] = $questionText;
            }
        }
    }

    $response = '';

    if ($totalQuestions == 0) {
        $response = "
        <div class='alert alert-danger' role='alert'>
          Please input questions
        </div>
    ";
    } elseif ($totalInserted > 0 && $duplicates == 0 && $invalidFormatCount == 0) {
        $response = "
        <div class='alert alert-success' role='alert'>
          $totalInserted Questions Submitted
        </div>
    ";
    }

    $last_response_parts = [];

    if ($totalQuestions > 0) {
        $last_response_parts[] = "<p>Total Questions: $totalQuestions</p>";
    }

    if ($totalInserted > 0) {
        $last_response_parts[] = "<p>Total Inserted: $totalInserted</p>";
    }

    if ($duplicates > 0) {
        $last_response_parts[] = "<p>Duplicate Questions: $duplicates</p>";
    }

    if ($invalidFormatCount > 0) {
        $last_response_parts[] = "<p>Invalid Format Questions: $invalidFormatCount</p>";
    }

    $last_response = '';

    if (!empty($last_response_parts)) {
        $last_response = "
        <br>
        <div class='alert alert-info'>
            " . implode("\n", $last_response_parts) . "
        </div>
    ";
    }

    echo $response . $last_response;
}
?>
