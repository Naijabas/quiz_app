<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">Submit Quiz Questions</h2>

    <!-- Sample Format Display -->
    <div class="alert alert-secondary">
        <strong>Sample Format:</strong>
        <p>Enter questions in the following format:</p>
        <pre>
Fill in the blanks: " The fear of man bringeth a ______: but whoso putteth his trust in the LORD shall be ______"
(a) snare, safe
(b) reward, judged
(c) hope, cursed
(d) charge, free
Correct answer: snare, safe</pre>
    </div>

    <form id="quizForm">
        <div class="mb-3">
            <label for="questions" class="form-label">Enter Questions and Answers:</label>
            <textarea id="questions" name="questions" rows="10" class="form-control" placeholder="Enter questions in the specified format..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
    <div id="results" class="mt-4"></div>
</div>

<script>
    $(document).ready(function() {
        $("#quizForm").on("submit", function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "process.php",
                data: $(this).serialize(),
                success: function(response) {
                    $("#results").html(response);
                    $("#questions").val("");
                },
                error: function() {
                    $("#results").html("<div class='alert alert-danger'>An error occurred.</div>");
                }
            });
        });
    });
</script>
</body>
</html>
