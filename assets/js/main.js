// StoneBoysClub Custom Client-Side Script

document.addEventListener('DOMContentLoaded', function() {
    // Initialize live validation for score editor
    const scoreModal = document.getElementById('scoreEditorModal');
    if (scoreModal) {
        setupScoreValidation();
    }
});

/**
 * Validates a completed set score according to Badminton rules:
 * - First to 30, must win by 2, capped at 40.
 */
function isValidCompletedSet(p1, p2) {
    if (p1 < 0 || p2 < 0 || p1 > 40 || p2 > 40) return false;
    
    // Standard win (30 points, opponent <= 28)
    if (p1 === 30 && p2 <= 28) return true;
    if (p2 === 30 && p1 <= 28) return true;
    
    // Win by 2 (between 31 and 40 points)
    if (p1 >= 31 && p1 <= 40 && (p1 - p2) === 2) return true;
    if (p2 >= 31 && p2 <= 40 && (p2 - p1) === 2) return true;
    
    // Cap win (40-39)
    if (p1 === 40 && p2 === 39) return true;
    if (p2 === 40 && p1 === 39) return true;
    
    return false;
}

/**
 * Validates an in-progress set score.
 */
function isValidInProgressSet(p1, p2) {
    if (p1 < 0 || p2 < 0 || p1 >= 40 || p2 >= 40) return false;
    
    // If a player reached 30 or more, the difference must be at most 1 (otherwise it is completed)
    if (p1 >= 30 && (p1 - p2) > 1) return false;
    if (p2 >= 30 && (p2 - p1) > 1) return false;
    
    // Cannot be already completed
    if (isValidCompletedSet(p1, p2)) return false;
    
    return true;
}

/**
 * Setup validation event listeners and visual feedback on the score editor modal.
 */
function setupScoreValidation() {
    const inputs = document.querySelectorAll('.score-input');
    const warningDiv = document.getElementById('scoreWarning');
    const submitBtn = document.getElementById('submitScoreBtn');
    
    inputs.forEach(input => {
        input.addEventListener('input', validateMatchScores);
    });
    
    function validateMatchScores() {
        let setsWon1 = 0;
        let setsWon2 = 0;
        let hasError = false;
        let errorMessage = "";
        let matchFinished = false;
        
        warningDiv.classList.add('d-none');
        warningDiv.innerText = "";
        submitBtn.disabled = false;
        
        // Loop through set 1 only (single set mode)
        for (let s = 1; s <= 1; s++) {
            const p1Val = document.getElementById(`set${s}_p1`).value.trim();
            const p2Val = document.getElementById(`set${s}_p2`).value.trim();
            
            const row = document.getElementById(`set${s}_row`);
            const statusText = document.getElementById(`set${s}_status`);
            
            // Reset row styles
            row.className = "row align-items-center mb-3 p-2 rounded";
            statusText.className = "small mt-1";
            statusText.innerText = "";
            
            // If completely empty, it is fine unless a winner was not decided yet
            if (p1Val === "" && p2Val === "") {
                if (matchFinished) {
                    statusText.innerText = "Not needed (Match Finished)";
                    statusText.classList.add('text-success');
                    row.classList.add('bg-slate-900', 'opacity-50');
                    // Disable inputs for unneeded sets
                    document.getElementById(`set${s}_p1`).disabled = true;
                    document.getElementById(`set${s}_p2`).disabled = true;
                } else {
                    statusText.innerText = "Empty";
                    statusText.classList.add('text-muted');
                    document.getElementById(`set${s}_p1`).disabled = false;
                    document.getElementById(`set${s}_p2`).disabled = false;
                }
                continue;
            }
            
            // Enable inputs by default
            document.getElementById(`set${s}_p1`).disabled = false;
            document.getElementById(`set${s}_p2`).disabled = false;
            
            // If only one score is filled
            if (p1Val === "" || p2Val === "") {
                hasError = true;
                errorMessage = `Set ${s} must have scores for both players.`;
                row.classList.add('bg-warning-subtle', 'text-dark');
                statusText.innerText = "Incomplete Score";
                statusText.classList.add('text-warning');
                continue;
            }
            
            const p1 = parseInt(p1Val, 10);
            const p2 = parseInt(p2Val, 10);
            
            if (isNaN(p1) || isNaN(p2) || p1 < 0 || p2 < 0) {
                hasError = true;
                errorMessage = `Scores for Set ${s} must be positive numbers.`;
                row.classList.add('bg-danger-subtle', 'text-dark');
                statusText.innerText = "Invalid Numbers";
                statusText.classList.add('text-danger');
                continue;
            }
            
            // Check completed set
            if (isValidCompletedSet(p1, p2)) {
                if (p1 > p2) {
                    setsWon1++;
                    statusText.innerText = `Set won by Player 1 (${p1}-${p2})`;
                } else {
                    setsWon2++;
                    statusText.innerText = `Set won by Player 2 (${p1}-${p2})`;
                }
                row.classList.add('bg-success-subtle', 'text-dark');
                statusText.classList.add('text-success');
                
                if (setsWon1 === 1 || setsWon2 === 1) {
                    matchFinished = true;
                }
            } 
            // Check in-progress set
            else if (isValidInProgressSet(p1, p2)) {
                if (matchFinished) {
                    hasError = true;
                    errorMessage = `Set ${s} cannot be scored because a winner has already been decided in previous sets.`;
                    row.classList.add('bg-danger-subtle', 'text-dark');
                    statusText.innerText = "Redundant Set";
                    statusText.classList.add('text-danger');
                } else {
                    row.classList.add('bg-info-subtle', 'text-dark');
                    statusText.innerText = "Set In Progress";
                    statusText.classList.add('text-info');
                    
                    // Verify subsequent sets are empty
                    for (let next = s + 1; next <= 3; next++) {
                        const np1 = document.getElementById(`set${next}_p1`).value.trim();
                        const np2 = document.getElementById(`set${next}_p2`).value.trim();
                        if (np1 !== "" || np2 !== "") {
                            hasError = true;
                            errorMessage = `Cannot score subsequent sets while Set ${s} is in progress.`;
                        }
                    }
                }
            } 
            // Invalid score
            else {
                hasError = true;
                errorMessage = `Invalid Badminton score for Set ${s} (e.g. max 40, must win by 2 above 30).`;
                row.classList.add('bg-danger-subtle', 'text-dark');
                statusText.innerText = "Invalid Badminton Score";
                statusText.classList.add('text-danger');
            }
        }
        
        // Final display validation
        if (hasError) {
            warningDiv.innerText = errorMessage;
            warningDiv.classList.remove('d-none');
            submitBtn.disabled = true;
        }
    }
    
    // Run initial validation once modal opens to set correct styles
    validateMatchScores();
}
