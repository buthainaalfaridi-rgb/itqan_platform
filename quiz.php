<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

if (isset($_GET['content_id'])) {
    $content_id = $_GET['content_id'];
    $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

    // Retrieve questions and options based on content_id
    $select_mcq = $conn->prepare("SELECT * FROM `mcq` WHERE content_id = ?");
    $select_mcq->execute([$content_id]);
    $questions = $select_mcq->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all options for each question
    foreach ($questions as &$question) {
        $select_options = $conn->prepare("SELECT * FROM `mcq_options` WHERE mcq_id = ?");
        $select_options->execute([$question['id']]);
        $options = $select_options->fetchAll(PDO::FETCH_ASSOC);

        $question['options'] = $options;
        foreach ($options as $option) {
            if ($option['is_correct'] == 1) {
                $question['correct_answer'] = $option['option_text'];
                break; // Get the first correct answer
            }
        }
    }
} else {
    echo "<p class='empty' data-ar='لا يوجد اختبار لهذا الدرس.' data-en='No quiz available for this lesson.'></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ar="اختبار" data-en="Quiz"></title>
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="css/all.min.css">
    <link id="style" rel="stylesheet" href="css/style-ar.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>
<section>

    <h1 class="heading" data-ar="اختبار" data-en="Quiz"></h1>
</section>

<div class="quiz-container">
    <?php if (!empty($questions)) : ?>
        <div class="question-status" id="question-status" data-ar="السؤال 1 من <?php echo count($questions); ?>" data-en="Question 1 of <?php echo count($questions); ?>">
            <?php // Display the first question by default ?>
            <?php
            $first_question = $questions[0];
            ?>
        </div>
        <div id="question-container">
            <div class='question' id='question-text'><?php echo htmlspecialchars($first_question['question']); ?></div>

            <?php
            $select_options = $conn->prepare("SELECT * FROM `mcq_options` WHERE mcq_id = ?");
            $select_options->execute([$first_question['id']]);
            $options = $select_options->fetchAll(PDO::FETCH_ASSOC);

            foreach ($options as $option) {
                echo "<div class='option' data-is-correct='{$option['is_correct']}' onclick='selectOption(this, 0)' data-option-index='{$option['id']}' >" . htmlspecialchars($option['option_text']) . "</div>";
            }
            ?>
        </div>

        <div class='nav-buttons'>
            <button onclick='previousQuestion()' class="inline-btn" id="prevButton" data-ar='السابق' data-en='Back' disabled>السابق</button>
            <button onclick='nextQuestion()' class="inline-btn" id="nextButton" data-ar='التالي' data-en='Next'>التالي</button>
        </div>
        <div id="score-container">
            <div id="score"></div>
            <button data-ar="إعادة الاختبار" data-en="Re-take Quiz" class="inline-option-btn" id="retake-button" onclick="retakeQuiz()">إعادة الاختبار</button>
        </div>
    <?php else : ?>
        <p class="empty" data-ar='لا يوجد اختبار لهذا الدرس.' data-en='No quiz available for this lesson.'></p>
    <?php endif; ?>
</div>

<script>
    const questions = <?php echo json_encode($questions); ?>;
    let currentQuestionIndex = 0;
    let selectedOptions = Array(questions.length).fill(null); // Store selected answers
    let correctAnswersCount = 0;
    let attemptNumber = 1; // Initialize attempt number

    function selectOption(element, questionIndex) {
        if (selectedOptions[questionIndex] !== null) return; // Prevent changing the answer

        const isCorrect = element.getAttribute('data-is-correct');
        const options = element.parentNode.querySelectorAll('.option');

        options.forEach(option => {
            option.classList.remove('correct');
            option.classList.remove('wrong');
            option.classList.remove('selected');

            if (option.getAttribute('data-is-correct') === '1') {
                option.classList.add('correct');
            }
        });

        if (isCorrect === '1') {
            element.classList.add('correct');
            correctAnswersCount++;
        } else {
            element.classList.add('wrong');
            const correctOption = element.parentNode.querySelector('.correct');
            if (correctOption) {
                correctOption.classList.add('selected');
            }
        }

        element.classList.add('selected');


        selectedOptions[questionIndex] = element; // Store selected option

        // Enable the "Next" button if an option is selected
        document.getElementById('nextButton').disabled = false;
    }

    function nextQuestion() {
        if (currentQuestionIndex < questions.length - 1) {
            currentQuestionIndex++;
            displayQuestion(currentQuestionIndex);
        } else if (currentQuestionIndex === questions.length - 1) {
            displayScore(); // Display the score when the user clicks "Next" on the last question
        }
    }

    function previousQuestion() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            displayQuestion(currentQuestionIndex);
        }
    }

    function displayQuestion(index) {
        const questionContainer = document.getElementById('question-container');
        const questionStatus = document.getElementById('question-status');
        questionContainer.classList.add('hidden');

        const currentLanguage = document.documentElement.lang; // Check the current language
        const questionText = currentLanguage === 'ar'
            ? `السؤال ${index + 1} من ${questions.length}`
            : `Question ${index + 1} of ${questions.length}`;

        questionStatus.textContent = questionText;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_options.php?mcq_id=' + questions[index].id, true);
        xhr.onload = function () {
            if (this.status == 200) {
                const options = JSON.parse(this.responseText);
                questionContainer.innerHTML = '';

                const questionText = document.createElement('div');
                questionText.className = 'question';
                questionText.textContent = questions[index].question;
                questionContainer.appendChild(questionText);

                options.forEach((option, optionIndex) => {
                    const optionElement = document.createElement('div');
                    optionElement.className = 'option';
                    optionElement.textContent = option.option_text;
                    optionElement.setAttribute('data-is-correct', option.is_correct);
                    optionElement.setAttribute('data-option-index', option['id']);
                    optionElement.onclick = function () { selectOption(this, index); };

                    // Check if this option was selected previously
                    if (selectedOptions[index] !== null && 
                        selectedOptions[index].getAttribute('data-option-index') == option['id']) {
                        optionElement.classList.add(selectedOptions[index].classList.contains('correct') ? 'correct' : 'wrong');
                        optionElement.classList.add('selected');
                    }

                    questionContainer.appendChild(optionElement);
                });

                questionContainer.classList.remove('hidden');
            }
        };
        xhr.send();

        document.getElementById('prevButton').disabled = index === 0;

        // Update nextButton state based on selectedOptions
        document.getElementById('nextButton').disabled = selectedOptions[index] === null;

    }

    function displayScore() {
        document.getElementById('question-container').style.display = 'none';
        document.getElementById('prevButton').style.display = 'none';
        document.getElementById('nextButton').style.display = 'none';
        document.getElementById('score-container').style.display = 'flex';

        updateScoreText(); // Call the function to update the score text

        const resultContainer = document.createElement('div');
        resultContainer.id = 'result-container';
        resultContainer.classList.add('result-container');


        // Add the attempt number before the result container
        const attemptText = document.createElement('p');
        attemptText.id ='attempt';
        attemptText.textContent = (document.documentElement.lang === 'ar' ? 'المحاولة ' + attemptNumber : 'Trying ' + attemptNumber) + ':';
        resultContainer.appendChild(attemptText);

        questions.forEach((question, index) => {
            const questionText = document.createElement('p');
            questionText.className = 'result-question';

            const userAnswerElement = selectedOptions[index];
            const userAnswerText = userAnswerElement ? userAnswerElement.textContent : 'لم يتم اختيار إجابة'; // Arabic: لم يتم اختيار إجابة
            const correctAnswerText = question.correct_answer ? question.correct_answer : 'الإجابة الصحيحة غير متاحة'; // Arabic: الإجابة الصحيحة غير متاحة

            const currentLanguage = document.documentElement.lang; // Get the current language

            const resultText = currentLanguage === 'ar'
                ? `السؤال ${index + 1}: ${question.question} 
        إجابتك كانت: ${userAnswerText}, 
        الإجابة الصحيحة كانت: ${correctAnswerText}.`
                : `The question ${index + 1}: ${question.question} 
        Your answer was: ${userAnswerText}, 
        The correct answer was: ${correctAnswerText}.`;

            questionText.textContent = resultText;
            resultContainer.appendChild(questionText);


            questionText.textContent = resultText;
            resultContainer.appendChild(questionText);
        });

        document.getElementById('score-container').appendChild(resultContainer);
    }

    function updateScoreText() {
        const scoreElement = document.getElementById('score');
        const currentLanguage = document.documentElement.lang; // Get the current language

        // Update the score text dynamically based on the current language
        const scoreText = currentLanguage === 'ar'
            ? `نتيجتك: ${correctAnswersCount} من ${questions.length}`
            : `Your score: ${correctAnswersCount} out of ${questions.length}`;

        scoreElement.textContent = scoreText;
    }

    function retakeQuiz() {
        currentQuestionIndex = 0;
        correctAnswersCount = 0;
        selectedOptions = Array(questions.length).fill(null);
        document.getElementById('question-container').style.display = 'block';
        document.getElementById('prevButton').style.display = 'inline-block';
        document.getElementById('nextButton').style.display = 'inline-block';
        document.getElementById('score-container').style.display = 'none';
        displayQuestion(currentQuestionIndex);
        attemptNumber++; // Increment attempt number on retake
    }
</script>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>
</body>
</html>