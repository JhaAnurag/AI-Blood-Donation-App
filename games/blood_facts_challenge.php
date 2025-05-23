<?php
include_once '../includes/header.php';
include_once '../includes/db.php';

// Check if the user is logged in
$user_logged_in = isset($_SESSION['donor_id']);
$user_name = $user_logged_in ? $_SESSION['donor_name'] : '';
$user_id = $user_logged_in ? $_SESSION['donor_id'] : '';
?>

<style>
/* Fix for navigation bar buttons */
nav .hidden.md\:flex {
    display: flex !important;
}
nav .md\:hidden {
    display: none !important;
}
nav .md\:block {
    display: block !important;
}
/* Ensure dropdown menus appear properly */
nav #hamburger-dropdown:not(.hidden) {
    display: block !important;
}
nav #mobile-menu:not(.hidden) {
    display: block !important;
}
</style>

<style>
/* Game specific styles */
.game-container {
    max-width: 800px;
    margin: 0 auto;
}

/* Base card styling without the hover effects */
.blood-facts-card {
    background-color: white !important;
    border-radius: 1rem !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08) !important;
    padding: 2rem !important;
    margin-bottom: 2rem !important;
    position: relative !important;
    z-index: 1 !important;
    transition: all 0.3s ease !important;
    border: 2px solid transparent !important;
}

/* Create a separate modifier class for the glow effect */
.has-glow-effect {
    overflow: visible !important;
    transform: none !important;
    transform-origin: center !important;
    backface-visibility: hidden !important;
    -webkit-font-smoothing: subpixel-antialiased !important;
}

/* Only apply pseudo-element to cards with the glow effect */
.has-glow-effect::before {
    content: '' !important;
    position: absolute !important;
    top: -4px !important;
    left: -4px !important;
    right: -4px !important;
    bottom: -4px !important;
    z-index: -1 !important;
    border-radius: inherit !important;
    background: linear-gradient(135deg, #6366F1, #4F46E5, #F97316, #EA580C) !important; /* Indigo to Coral */
    background-size: 300% 300% !important;
    opacity: 0 !important;
    transition: opacity 0.5s ease !important;
    pointer-events: none !important;
}

/* Only apply hover effects to cards with the glow effect */
.has-glow-effect:hover {
    transform: translateY(-12px) !important;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15), 0 10px 15px rgba(0, 0, 0, 0.08) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
}

.has-glow-effect:hover::before {
    opacity: 1 !important;
    animation: gradient-shift 3s infinite linear !important;
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Add this to ensure hover effects don't impact nearby elements */
.blood-facts-card-container {
    isolation: isolate !important;
}

/* Dark mode styling for cards */
.dark .blood-facts-card {
    background-color: #2d3748 !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25) !important;
}

.dark .has-glow-effect::before {
    background: linear-gradient(135deg, #ff5757, #ffcc00, #ff57b4, #4d94ff) !important;
    background-size: 300% 300% !important;
}

/* Keeping the original question-card style for backward compatibility */
.question-card {
    background-color: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

/* Apply the glow effect classes selectively in your HTML */

/* Score display styling */
.score-display {
    font-size: 1.25rem;
    font-weight: bold;
    color: #2d3748;
}

.dark .score-display {
    color: #f7fafc;
}

/* Progress bar styling */
.progress-container {
    width: 100%;
    height: 8px;
    background-color: #edf2f7;
    border-radius: 4px;
    margin-bottom: 1rem;
    overflow: hidden;
}

.dark .progress-container {
    background-color: #4a5568;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #6366F1, #F97316); /* Indigo to Coral */
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Answer buttons styling */
.answer-btn {
    display: block;
    width: 100%;
    text-align: left;
    background-color: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
    cursor: pointer;
    color: #2d3748;
}

.dark .answer-btn {
    background-color: #4a5568;
    border-color: #2d3748;
    color: #e2e8f0;
}

.answer-btn:hover {
    background-color: #edf2f7;
}

.dark .answer-btn:hover {
    background-color: #3c4b5e;
}

.answer-btn.correct {
    background-color: #c6f6d5;
    border-color: #68d391;
    color: #22543d;
}

.dark .answer-btn.correct {
    background-color: #276749;
    border-color: #68d391;
    color: #f0fff4;
}

.answer-btn.incorrect {
    background-color: #fed7d7;
    border-color: #fc8181;
    color: #822727;
}

.dark .answer-btn.incorrect {
    background-color: #7b2e2e;
    border-color: #fc8181;
    color: #fff5f5;
}

/* Fact card styling */
.fact-card {
    background-color: #ebf4ff;
    border-left: 4px solid #4299e1;
    padding: 1rem;
    margin-top: 1.5rem;
    border-radius: 0.25rem;
    display: none;
}

.dark .fact-card {
    background-color: #2c3e50;
    border-left: 4px solid #3182ce;
}

#fact-text {
    color: #2a4365;
    margin: 0;
}

.dark #fact-text {
    color: #bee3f8;
}

/* Heart animation */
.heart-container {
    width: 100px;
    height: 100px;
    position: relative;
    margin: 2rem auto;
}

.heart {
    background-color: #e53e3e;
    height: 50px;
    width: 50px;
    transform: rotate(-45deg);
    position: absolute;
    top: 50%;
    left: 50%;
    margin: -25px 0 0 -25px;
}

.heart:before,
.heart:after {
    content: "";
    background-color: #e53e3e;
    border-radius: 50%;
    height: 50px;
    position: absolute;
    width: 50px;
}

.heart:before {
    top: -25px;
    left: 0;
}

.heart:after {
    left: 25px;
    top: 0;
}

@keyframes pulse-initial {
    0% { transform: rotate(-45deg) scale(1); }
    15% { transform: rotate(-45deg) scale(1.25); }
    30% { transform: rotate(-45deg) scale(1); }
    100% { transform: rotate(-45deg) scale(1); }
}

@keyframes pulse-fast {
    0% { transform: rotate(-45deg) scale(1); }
    15% { transform: rotate(-45deg) scale(1.35); }
    30% { transform: rotate(-45deg) scale(1); }
    100% { transform: rotate(-45deg) scale(1); }
}

@keyframes pulse-superfast {
    0% { transform: rotate(-45deg) scale(1); }
    20% { transform: rotate(-45deg) scale(1.45); }
    40% { transform: rotate(-45deg) scale(1); }
    100% { transform: rotate(-45deg) scale(1); }
}

/* Leaderboard styling */
.leaderboard {
    background-color: white;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: 100%;
}

.dark .leaderboard {
    background-color: #2d3748;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

.leaderboard-item {
    display: grid;
    grid-template-columns: 80px 1fr 100px;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    align-items: center;
}

.dark .leaderboard-item {
    border-bottom: 1px solid #4a5568;
}

.leaderboard-item:last-child {
    border-bottom: none;
}

.leaderboard-item .rank {
    font-weight: bold;
    color: #4a5568;
    text-align: center;
}

.dark .leaderboard-item .rank {
    color: #e2e8f0;
}

.leaderboard-item .name {
    color: #2d3748;
}

.dark .leaderboard-item .name {
    color: #f7fafc;
}

.leaderboard-item .score {
    text-align: right;
    font-weight: bold;
    color: #e53e3e;
}

.dark .leaderboard-item .score {
    color: #fc8181;
}

.leaderboard-item.bg-yellow-100.dark\:bg-yellow-900 .name,
.leaderboard-item.bg-yellow-100.dark\:bg-yellow-900 .rank {
    color: #744210;
}

.dark .leaderboard-item.bg-yellow-100.dark\:bg-yellow-900 .name,
.dark .leaderboard-item.bg-yellow-100.dark\:bg-yellow-900 .rank {
    color: #fefcbf;
}

/* Sharing buttons */
.sharing-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 1.5rem 0;
}

.sharing-buttons button {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    color: white;
    font-weight: 500;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}

.sharing-buttons .facebook {
    background-color: #1877f2;
}

.sharing-buttons .twitter {
    background-color: #1da1f2;
}

.sharing-buttons .whatsapp {
    background-color: #25d366;
}

.sharing-buttons button i {
    margin-right: 0.5rem;
}

/* Animation for confetti */
@keyframes float-down {
    0% {
        transform: translateY(-100vh) rotate(0deg);
    }
    100% {
        transform: translateY(100vh) rotate(360deg);
    }
}

.confetti-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1000;
    overflow: hidden;
}

/* Additional dark mode fixes for question card */
.dark #question-card {
    background-color: #2d3748;
    color: #f7fafc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);
}

/* Badge notification styling */
#badge-notification {
    animation-duration: 0.5s;
}

/* Fade in animation */
.fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<div class="py-8">
    <h1 class="text-3xl font-bold text-center mb-8 text-gray-900 dark:text-white">Blood Facts Challenge</h1>
    <p class="text-center text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">Test your knowledge about blood donation and facts! Answer questions correctly to make the heart beat faster. How many can you get right?</p>
    <div class="game-container">
        <!-- Start Screen -->
        <div id="start-screen" class="text-center">
            <div class="heart-container">
                <div class="heart"></div>
            </div>
            <h2 class="text-2xl font-bold mb-6 dark:text-white">Ready to Play?</h2>
            <p class="mb-8 text-gray-600 dark:text-gray-300">Answer 10 questions about blood donation and blood facts. For every correct answer, the heart beats faster!</p>
            <button id="start-button" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-md">
                Start Challenge
            </button>
        </div>
        <!-- Game Screen -->
        <div id="game-screen" class="hidden">
            <div class="mb-6 flex items-center justify-between">
                <div class="score-display">Score: <span id="score">0</span></div>
                <div class="text-gray-700 dark:text-gray-300">Question <span id="current-question">1</span>/10</div>
            </div>
            <div class="progress-container">
                <div id="progress-bar" class="progress-bar"></div>
            </div>
            <div class="heart-container">
                <div id="game-heart" class="heart"></div>
            </div>
            <div id="question-card" class="question-card no-hover-effect">
                <h3 id="question-text" class="text-xl font-semibold mb-4 dark:text-white">Question text goes here?</h3>
                <div id="answers-container">
                    <!-- Answers will be inserted here -->
                </div>
                <div id="fact-card" class="fact-card">
                    <p id="fact-text" class="text-sm"></p>
                </div>
            </div>
            <div class="text-center">
                <button id="next-button" class="hidden bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-md">
                    Next Question
                </button>
            </div>
        </div>
        <!-- Results Screen -->
        <div id="results-screen" class="hidden text-center">
            <h2 class="text-2xl font-bold mb-4 dark:text-white">Challenge Complete!</h2>
            <div class="heart-container">
                <div id="results-heart" class="heart"></div>
            </div>
            <p class="mb-2 text-lg dark:text-white">Your Score:</p>
            <div class="score-display text-3xl mb-6"><span id="final-score">0</span>/10</div>
            <p id="score-message" class="mb-6 text-gray-700 dark:text-gray-300">Great job! You know your blood facts!</p>
            <?php if($user_logged_in): ?>
                <button id="save-score-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition shadow-md">
                    Save Score to Leaderboard
                </button>
            <?php else: ?>
            <div class="mb-6 text-gray-700 dark:text-gray-300">
                <p><a href="<?php echo $base_url; ?>login.php" class="text-orange-600 dark:text-orange-400 underline">Log in</a> to save your score to the leaderboard!</p>
            </div>
            <?php endif; ?>
            
            <div class="sharing-buttons">
                <button class="facebook" onclick="shareScore('facebook')">
                    <i class="fab fa-facebook-f"></i> Share
                </button>
                <button class="twitter" onclick="shareScore('twitter')">
                    <i class="fab fa-twitter"></i> Tweet
                </button>
                <button class="whatsapp" onclick="shareScore('whatsapp')">
                    <i class="fab fa-whatsapp"></i> Share
                </button>
            </div>
            
            <div class="mt-8">
                <button id="play-again-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-md">
                    <i class="fas fa-redo mr-2"></i> Play Again
                </button>
            </div>
        </div>
        <!-- Leaderboard Section -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6 text-center dark:text-white">Leaderboard</h2>
            <div class="leaderboard">
                <div class="leaderboard-item bg-gray-100 dark:bg-gray-700">
                    <div class="rank">#</div>
                    <div class="name font-semibold">Name</div>
                    <div class="score">Score</div>
                </div>
                <div id="leaderboard-list">
                    <!-- Leaderboard items will be inserted here -->
                    <div class="leaderboard-item animate-pulse">
                        <div class="rank">1</div>
                        <div class="name">Loading...</div>
                        <div class="score">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<!-- Simple confetti effect container -->
<div id="confetti-container" class="confetti-container"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const startScreen = document.getElementById('start-screen');
    const gameScreen = document.getElementById('game-screen');
    const resultsScreen = document.getElementById('results-screen');
    const startButton = document.getElementById('start-button');
    const nextButton = document.getElementById('next-button');
    const playAgainBtn = document.getElementById('play-again-btn');
    const saveScoreBtn = document.getElementById('save-score-btn');
    const questionCard = document.getElementById('question-card');
    const questionText = document.getElementById('question-text');
    const answersContainer = document.getElementById('answers-container');
    const scoreDisplay = document.getElementById('score');
    const finalScoreDisplay = document.getElementById('final-score');
    const currentQuestionDisplay = document.getElementById('current-question');
    const progressBar = document.getElementById('progress-bar');
    const factCard = document.getElementById('fact-card');
    const factText = document.getElementById('fact-text');
    const scoreMessage = document.getElementById('score-message');
    const gameHeart = document.getElementById('game-heart');
    const resultsHeart = document.getElementById('results-heart');
    const leaderboardList = document.getElementById('leaderboard-list');
    const confettiContainer = document.getElementById('confetti-container');

    // Game State
    let currentQuestion = 0;
    let score = 0;
    let questions = [];
    let answeredCorrectly = false;
    let userLoggedIn = <?php echo $user_logged_in ? 'true' : 'false'; ?>;
    let userName = "<?php echo $user_name; ?>";
    let userId = "<?php echo $user_id; ?>";

    // Blood Facts Questions
    const bloodFactsQuestions = [
        {
            question: "How many pints of blood does the average adult human body contain?",
            answers: ["6 pints", "8-10 pints", "12-14 pints", "15-18 pints"],
            correctAnswer: 1,
            fact: "The average adult human body contains about 8-10 pints of blood, which is approximately 4.5 to 5.7 liters."
        },
        {
            question: "What percentage of your body weight is blood?",
            answers: ["4-5%", "7-8%", "10-12%", "15-17%"],
            correctAnswer: 1,
            fact: "Blood makes up approximately 7-8% of your total body weight."
        },
        {
            question: "Which blood type is considered the universal donor?",
            answers: ["A+", "B-", "AB+", "O-"],
            correctAnswer: 3,
            fact: "O- is the universal donor because it has no A or B antigens and no Rh factor, making it compatible with all other blood types."
        },
        {
            question: "Which blood type is considered the universal recipient?",
            answers: ["O+", "AB+", "A-", "B+"],
            correctAnswer: 1,
            fact: "AB+ is the universal recipient because it has both A and B antigens and the Rh factor, allowing it to receive blood from any type."
        },
        {
            question: "How often can a person donate whole blood?",
            answers: ["Every 2 weeks", "Every 56 days (8 weeks)", "Every 4 months", "Every 6 months"],
            correctAnswer: 1,
            fact: "Donors must wait at least 56 days (8 weeks) between whole blood donations to allow their bodies to replenish the red blood cells."
        },
        {
            question: "What is the most common blood type?",
            answers: ["A+", "B+", "O+", "AB-"],
            correctAnswer: 2,
            fact: "O+ is the most common blood type, with about 38% of the population having this type."
        },
        {
            question: "How long does the actual blood donation process take?",
            answers: ["5-10 minutes", "10-15 minutes", "20-30 minutes", "45-60 minutes"],
            correctAnswer: 1,
            fact: "The actual blood donation typically takes only 10-15 minutes, though the entire process including screening and paperwork takes about an hour."
        },
        {
            question: "What is the shelf life of donated red blood cells?",
            answers: ["24 hours", "1 week", "42 days", "1 year"],
            correctAnswer: 2,
            fact: "Red blood cells can be stored for up to 42 days when refrigerated properly."
        },
        {
            question: "Which component of blood carries oxygen throughout the body?",
            answers: ["White blood cells", "Platelets", "Red blood cells", "Plasma"],
            correctAnswer: 2,
            fact: "Red blood cells contain hemoglobin, which binds with oxygen and transports it throughout the body."
        },
        {
            question: "How many lives can be saved with one blood donation?",
            answers: ["1 life", "Up to 3 lives", "Up to 5 lives", "Up to 10 lives"],
            correctAnswer: 1,
            fact: "A single blood donation can save up to 3 lives because blood is separated into red cells, platelets, and plasma that can be used for different patients."
        },
        {
            question: "What percentage of the world's population is eligible to donate blood?",
            answers: ["Less than 38%", "About 50%", "About 65%", "Over 80%"],
            correctAnswer: 0,
            fact: "Less than 38% of the world's population is eligible to donate blood due to various health conditions, medications, and age restrictions."
        },
        {
            question: "What component of blood helps in blood clotting?",
            answers: ["Red blood cells", "White blood cells", "Platelets", "Plasma"],
            correctAnswer: 2,
            fact: "Platelets are tiny cell fragments that help the blood clotting process by forming plugs in blood vessel holes."
        },
        {
            question: "What is the rarest blood type?",
            answers: ["O-", "B-", "AB-", "A-"],
            correctAnswer: 2,
            fact: "AB- is the rarest blood type, with less than 1% of the population having this type."
        },
        {
            question: "How many main blood groups are in the ABO system?",
            answers: ["2", "4", "6", "8"],
            correctAnswer: 1,
            fact: "There are 4 main blood groups in the ABO system: A, B, AB, and O."
        },
        {
            question: "Approximately how many units of blood are needed every day in the U.S.?",
            answers: ["13,000", "36,000", "52,000", "75,000"],
            correctAnswer: 1,
            fact: "About 36,000 units of red blood cells are needed every day in the U.S., with nearly 21 million blood components transfused each year."
        }
    ];

    // Initialize the Game
    function initGame() {
        // Shuffle and select 10 questions
        questions = shuffleArray(bloodFactsQuestions).slice(0, 10);
        // Reset game state
        currentQuestion = 0;
        score = 0;
        // Update UI
        scoreDisplay.textContent = score;
        currentQuestionDisplay.textContent = currentQuestion + 1;
        progressBar.style.width = `${((currentQuestion) / questions.length) * 100}%`;
        // Load first question
        loadQuestion();
        // Reset heart animation
        gameHeart.style.animation = "pulse-initial 1.5s infinite ease-in-out";
        // Fetch leaderboard data
        fetchLeaderboard();
    }

    // Load Question
    function loadQuestion() {
        const question = questions[currentQuestion];
        answeredCorrectly = false;
        // Reset UI
        factCard.style.display = 'none';
        nextButton.classList.add('hidden');
        // Set question text
        questionText.textContent = question.question;
        // Clear previous answers
        answersContainer.innerHTML = '';
        
        // Add answer buttons
        question.answers.forEach((answer, index) => {
            const button = document.createElement('button');
            button.className = 'answer-btn';
            button.textContent = answer;
            button.dataset.index = index;
            button.addEventListener('click', handleAnswerClick);
            answersContainer.appendChild(button);
        });
        // Add fade-in animation
        questionCard.classList.add('fade-in');
        setTimeout(() => {
            questionCard.classList.remove('fade-in');
        }, 500);
    }

    // Handle Answer Click
    function handleAnswerClick(e) {
        if (answeredCorrectly) return;
        const selectedIndex = parseInt(e.target.dataset.index);
        const correctIndex = questions[currentQuestion].correctAnswer;
        
        // Disable all buttons
        const buttons = answersContainer.querySelectorAll('.answer-btn');
        buttons.forEach(button => {
            button.removeEventListener('click', handleAnswerClick);
        });
        
        // Mark correct and incorrect answers
        buttons.forEach((button, index) => {
            if (index === correctIndex) {
                button.classList.add('correct');
            } else if (index === selectedIndex) {
                button.classList.add('incorrect');
            }
        });
        
        // Show fact
        factText.textContent = questions[currentQuestion].fact;
        factCard.style.display = 'block';
        
        // Update score and animate heart if correct
        if (selectedIndex === correctIndex) {
            score++;
            scoreDisplay.textContent = score;
            answeredCorrectly = true;
            
            // Update heart animation based on score
            if (score <= 3) {
                gameHeart.style.animation = "pulse-initial 1.5s infinite ease-in-out";
            } else if (score <= 6) {
                gameHeart.style.animation = "pulse-fast 1.2s infinite ease-in-out";
            } else {
                gameHeart.style.animation = "pulse-superfast 0.8s infinite ease-in-out";
            }
        }
        
        // Show next button
        nextButton.classList.remove('hidden');
    }

    // Handle Next Question
    function nextQuestion() {
        currentQuestion++;
        // Update progress
        currentQuestionDisplay.textContent = currentQuestion + 1;
        progressBar.style.width = `${((currentQuestion) / questions.length) * 100}%`;
        if (currentQuestion < questions.length) {
            loadQuestion();
        } else {
            showResults();
        }
    }

    // Show Results
    function showResults() {
        gameScreen.classList.add('hidden');
        resultsScreen.classList.remove('hidden');
        finalScoreDisplay.textContent = score;
        // Set heart animation based on final score
        if (score <= 3) {
            resultsHeart.style.animation = "pulse-initial 1.5s infinite ease-in-out";
            scoreMessage.textContent = "Keep learning about blood donation!";
        } else if (score <= 6) {
            resultsHeart.style.animation = "pulse-fast 1.2s infinite ease-in-out";
            scoreMessage.textContent = "Good job! You know some blood facts!";
        } else if (score <= 9) {
            resultsHeart.style.animation = "pulse-superfast 0.8s infinite ease-in-out";
            scoreMessage.textContent = "Great job! You really know your blood facts!";
        } else {
            resultsHeart.style.animation = "pulse-superfast 0.6s infinite ease-in-out";
            scoreMessage.textContent = "Perfect score! You're a blood donation expert!";
            createConfetti();
        }
        // Enable or disable save score button
        if (userLoggedIn && saveScoreBtn) {
            saveScoreBtn.disabled = false;
        }
    }
        
    // Save Score to Leaderboard
    function saveScore() {
        if (!userLoggedIn) return;
        // Disable button to prevent multiple submissions
        saveScoreBtn.disabled = true;
        saveScoreBtn.textContent = 'Saving...';
        
        // Send score to server
        fetch('save_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `score=${score}&user_id=${userId}&game=blood_facts_challenge`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                saveScoreBtn.textContent = 'Score Saved!';
                fetchLeaderboard(); // Refresh leaderboard
                // Check if user earned any new badges
                if (data.new_badges && data.new_badges.length > 0) {
                    showBadgeNotification(data.new_badges);
                }
            } else {
                saveScoreBtn.textContent = 'Error Saving Score';
                console.error('Error saving score:', data.error);
            }
        })
        .catch(error => {
            saveScoreBtn.textContent = 'Error Saving Score';
            console.error('Error saving score:', error);
        });
    }

    // Show badge notification
    function showBadgeNotification(badges) {
        // Create notification container if it doesn't exist
        let notificationContainer = document.getElementById('badge-notification');
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.id = 'badge-notification';
            notificationContainer.className = 'fixed top-20 right-5 z-50 w-80 transform transition-all duration-500';
            document.body.appendChild(notificationContainer);
        }

        // Create notifications for each new badge
        badges.forEach(badge => {
            const notification = document.createElement('div');
            notification.className = 'bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 mb-4 border-l-4 border-yellow-400 animate__animated animate__fadeInRight';
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="mr-4 bg-yellow-400 rounded-full p-2 text-yellow-900">
                        <i class="fas fa-award text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">New Badge Unlocked!</h4>
                        <p class="text-gray-700 dark:text-gray-300">${badge.name}</p>
                    </div>
                </div>
                <a href="../dashboard/achievements.php" class="mt-2 text-sm text-blue-600 dark:text-blue-400 block text-right">View All Achievements</a>
            `;
            notificationContainer.appendChild(notification);
            // Remove notification after 5 seconds
            setTimeout(() => {
                notification.classList.add('animate__fadeOutRight');
                setTimeout(() => {
                    notification.remove();
                }, 1000);
            }, 5000);
        });
    }
    
    // Fetch Leaderboard
    function fetchLeaderboard() {
        fetch('get_leaderboard.php?game=blood_facts_challenge')
        .then(response => response.json())
        .then(data => {
            // Clear leaderboard
            leaderboardList.innerHTML = '';
            
            if (data.length === 0) {
                const noScoresItem = document.createElement('div');
                noScoresItem.className = 'leaderboard-item';
                noScoresItem.innerHTML = `
                    <div class="rank">-</div>
                    <div class="name">No scores yet</div>
                    <div class="score">-</div>
                `;
                leaderboardList.appendChild(noScoresItem);
            } else {
                // Add leaderboard items
                data.forEach((item, index) => {
                    const leaderboardItem = document.createElement('div');
                    leaderboardItem.className = 'leaderboard-item';
                    // Highlight current user
                    if (userLoggedIn && item.user_id == userId) {
                        leaderboardItem.className += ' bg-yellow-100 dark:bg-yellow-900';
                    }
                    leaderboardItem.innerHTML = `
                        <div class="rank">${index + 1}</div>
                        <div class="name">${item.name}</div>
                        <div class="score">${item.score}/10</div>
                    `;
                    leaderboardList.appendChild(leaderboardItem);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching leaderboard:', error);
            leaderboardList.innerHTML = `
                <div class="leaderboard-item">
                    <div class="rank">-</div>
                    <div class="name">Error loading leaderboard</div>
                    <div class="score">-</div>
                </div>
            `;
        });
    }

    // Share Score
    function shareScore(platform) {
        const message = `I scored ${score}/10 on the Blood Facts Challenge! Test your knowledge about blood donation too!`;
        const url = window.location.href;
        let shareUrl;
        switch (platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(message)}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(message)}&url=${encodeURIComponent(url)}`;
                break;
            case 'whatsapp':
                shareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(message + ' ' + url)}`;
                break;
        }
        window.open(shareUrl, '_blank');
    }

    // Create confetti effect
    function createConfetti() {
        const colors = ['#e53e3e', '#fc8181', '#f56565', '#feb2b2', '#ffffff'];
        // Create 100 confetti pieces
        for (let i = 0; i < 100; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'absolute';
            confetti.style.width = `${Math.random() * 10 + 5}px`;
            confetti.style.height = `${Math.random() * 10 + 5}px`;
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = `${Math.random() * 100}vw`;
            confetti.style.opacity = Math.random();
            confetti.style.borderRadius = '50%';
            
            // Animation
            confetti.style.animation = `float-down ${Math.random() * 3 + 2}s linear forwards`;
            
            // Append to container
            confettiContainer.appendChild(confetti);
        }
        
        // Clear confetti after animation
        setTimeout(() => {
            confettiContainer.innerHTML = '';
        }, 5000);
    }
    
    // Utility function to shuffle array
    function shuffleArray(array) {
        const newArray = [...array];
        for (let i = newArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
        }
        return newArray;
    }

    // Event Listeners
    startButton.addEventListener('click', function() {
        startScreen.classList.add('hidden');
        gameScreen.classList.remove('hidden');
        initGame();
    });
    
    nextButton.addEventListener('click', nextQuestion);
    
    playAgainBtn.addEventListener('click', function() {
        resultsScreen.classList.add('hidden');
        gameScreen.classList.remove('hidden');
        initGame();
    });
    
    if (saveScoreBtn) {
        saveScoreBtn.addEventListener('click', saveScore);
    }
    
    // Make share function globally available
    window.shareScore = shareScore;

    // Initialize leaderboard on page load
    fetchLeaderboard();
});
</script>

<!-- Add navigation and dark mode toggle functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Hamburger menu dropdown
    const hamburgerButton = document.getElementById('hamburger-menu');
    const hamburgerDropdown = document.getElementById('hamburger-dropdown');
    if (hamburgerButton && hamburgerDropdown) {
        hamburgerButton.addEventListener('click', function(e) {
            e.stopPropagation();
            hamburgerDropdown.classList.toggle('hidden');
        });
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            if (!hamburgerDropdown.classList.contains('hidden')) {
                hamburgerDropdown.classList.add('hidden');
            }
        });
    }
    
    // Initialize dark mode toggle if it exists
    if (typeof window.toggleDarkMode === 'function') {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeToggleMobile = document.getElementById('darkModeToggleMobile');
        
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', window.toggleDarkMode);
        }
        
        if (darkModeToggleMobile) {
            darkModeToggleMobile.addEventListener('click', window.toggleDarkMode);
        }
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>