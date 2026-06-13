<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matchId = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
    $tournamentId = isset($_POST['tournament_id']) ? intval($_POST['tournament_id']) : 0;
    
    $setScores = [
        1 => [
            'p1' => isset($_POST['set1_p1']) && $_POST['set1_p1'] !== '' ? $_POST['set1_p1'] : null,
            'p2' => isset($_POST['set1_p2']) && $_POST['set1_p2'] !== '' ? $_POST['set1_p2'] : null,
        ],
        2 => [
            'p1' => isset($_POST['set2_p1']) && $_POST['set2_p1'] !== '' ? $_POST['set2_p1'] : null,
            'p2' => isset($_POST['set2_p2']) && $_POST['set2_p2'] !== '' ? $_POST['set2_p2'] : null,
        ],
        3 => [
            'p1' => isset($_POST['set3_p1']) && $_POST['set3_p1'] !== '' ? $_POST['set3_p1'] : null,
            'p2' => isset($_POST['set3_p2']) && $_POST['set3_p2'] !== '' ? $_POST['set3_p2'] : null,
        ]
    ];
    
    $db = getDBConnection();
    
    $result = updateMatchScore($db, $matchId, $setScores);
    
    if ($result['success']) {
        $_SESSION['feedback_message'] = $result['message'];
        $_SESSION['feedback_type'] = "success";
    } else {
        $_SESSION['feedback_message'] = $result['message'];
        $_SESSION['feedback_type'] = "danger";
    }
    
    header("Location: tournament.php?id=" . $tournamentId);
    exit;
} else {
    header("Location: index.php");
    exit;
}
