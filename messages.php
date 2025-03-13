<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = '';
$success_message = '';
$current_folder = $_GET['folder'] ?? 'inbox';
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send') {
    $recipient_id = sanitize_input($_POST['recipient_id']);
    $recipient_type = sanitize_input($_POST['recipient_type']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO messages (sender_id, sender_type, recipient_id, recipient_type, subject, message)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$user_id, $user_type, $recipient_id, $recipient_type, $subject, $message])) {
            $success_message = "Message sent successfully!";
        } else {
            $error_message = "Failed to send message. Please try again.";
        }
    } catch (PDOException $e) {
        error_log("Error sending message: " . $e->getMessage());
        $error_message = "An error occurred while sending the message.";
    }
}

// Get messages based on folder
try {
    if ($current_folder === 'inbox') {
        $stmt = $pdo->prepare("
            SELECT m.*, 
                   CASE 
                       WHEN m.sender_type = 'employer' THEN e.company_name
                       ELSE js.full_name 
                   END as sender_name
            FROM messages m
            LEFT JOIN employers e ON m.sender_id = e.id AND m.sender_type = 'employer'
            LEFT JOIN job_seekers js ON m.sender_id = js.id AND m.sender_type = 'job_seeker'
            WHERE m.recipient_id = ? 
            AND m.recipient_type = ?
            AND m.deleted_by_recipient = FALSE
            ORDER BY m.created_at DESC
        ");
    } else {
        $stmt = $pdo->prepare("
            SELECT m.*, 
                   CASE 
                       WHEN m.recipient_type = 'employer' THEN e.company_name
                       ELSE js.full_name 
                   END as recipient_name
            FROM messages m
            LEFT JOIN employers e ON m.recipient_id = e.id AND m.recipient_type = 'employer'
            LEFT JOIN job_seekers js ON m.recipient_id = js.id AND m.recipient_type = 'job_seeker'
            WHERE m.sender_id = ? 
            AND m.sender_type = ?
            AND m.deleted_by_sender = FALSE
            ORDER BY m.created_at DESC
        ");
    }
    
    $stmt->execute([$user_id, $user_type]);
    $messages = $stmt->fetchAll();

    // Get potential recipients for compose form
    $recipients_stmt = $pdo->prepare("
        SELECT id, company_name as name, 'employer' as type 
        FROM employers 
        WHERE id != ? 
        UNION 
        SELECT id, full_name as name, 'job_seeker' as type 
        FROM job_seekers 
        WHERE id != ?
        ORDER BY name
    ");
    $recipients_stmt->execute([$user_id, $user_id]);
    $potential_recipients = $recipients_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Error fetching messages: " . $e->getMessage());
    $error_message = "An error occurred while fetching messages.";
}

require_once 'includes/header.php';
?>

<main class="messages-page">
    <div class="container">
        <h1>Messages</h1>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="messages-container">
            <!-- Sidebar -->
            <div class="messages-sidebar">
                <button class="compose-btn" onclick="showComposeForm()">
                    <i class="fas fa-pen"></i> Compose
                </button>
                
                <nav class="folder-nav">
                    <a href="?folder=inbox" class="<?php echo $current_folder === 'inbox' ? 'active' : ''; ?>">
                        <i class="fas fa-inbox"></i> Inbox
                    </a>
                    <a href="?folder=sent" class="<?php echo $current_folder === 'sent' ? 'active' : ''; ?>">
                        <i class="fas fa-paper-plane"></i> Sent
                    </a>
                </nav>
            </div>

            <!-- Message List -->
            <div class="message-list">
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="message-item <?php echo $message['read_at'] ? '' : 'unread'; ?>" 
                             onclick="showMessage(<?php echo htmlspecialchars(json_encode($message)); ?>)">
                            <div class="message-sender">
                                <?php echo $current_folder === 'inbox' ? 
                                    htmlspecialchars($message['sender_name']) : 
                                    htmlspecialchars($message['recipient_name']); ?>
                            </div>
                            <div class="message-subject">
                                <?php echo htmlspecialchars($message['subject']); ?>
                            </div>
                            <div class="message-date">
                                <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-messages">No messages in this folder.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Compose Message Modal -->
        <div id="composeModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="hideComposeForm()">&times;</span>
                <h2>Compose Message</h2>
                <form action="messages.php" method="POST">
                    <input type="hidden" name="action" value="send">
                    
                    <div class="form-group">
                        <label for="recipient">To:</label>
                        <select name="recipient_id" id="recipient" required>
                            <option value="">Select recipient</option>
                            <?php foreach ($potential_recipients as $recipient): ?>
                                <option value="<?php echo $recipient['id']; ?>" 
                                        data-type="<?php echo $recipient['type']; ?>">
                                    <?php echo htmlspecialchars($recipient['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="recipient_type" id="recipient_type">
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Send</button>
                        <button type="button" class="btn btn-secondary" onclick="hideComposeForm()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Message Modal -->
        <div id="viewModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="hideViewMessage()">&times;</span>
                <div id="messageContent">
                    <!-- Message content will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.messages-page {
    padding: 2rem 0;
    background-color: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.messages-container {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-top: 2rem;
}

/* Sidebar Styles */
.messages-sidebar {
    padding: 1.5rem;
    border-right: 1px solid #dee2e6;
}

.compose-btn {
    width: 100%;
    padding: 0.75rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.compose-btn:hover {
    background: #0056b3;
}

.folder-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.folder-nav a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    color: #495057;
    text-decoration: none;
    border-radius: 5px;
}

.folder-nav a:hover {
    background: #f8f9fa;
}

.folder-nav a.active {
    background: #e9ecef;
    font-weight: 500;
}

/* Message List Styles */
.message-list {
    padding: 1.5rem;
}

.message-item {
    display: grid;
    grid-template-columns: 200px 1fr 150px;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    cursor: pointer;
}

.message-item:hover {
    background: #f8f9fa;
}

.message-item.unread {
    background: #fff8e6;
    font-weight: 500;
}

.message-sender {
    font-weight: 500;
}

.message-subject {
    color: #495057;
}

.message-date {
    color: #6c757d;
    text-align: right;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background: white;
    margin: 5% auto;
    padding: 2rem;
    width: 90%;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.close {
    position: absolute;
    right: 1rem;
    top: 1rem;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 1rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.no-messages {
    text-align: center;
    color: #666;
    padding: 2rem;
}

/* Message View Styles */
#messageContent {
    padding: 1rem 0;
}

.message-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.message-header h3 {
    margin-bottom: 0.5rem;
}

.message-meta {
    color: #666;
    font-size: 0.9rem;
}

.message-body {
    white-space: pre-wrap;
    line-height: 1.6;
}
</style>

<script>
function showComposeForm() {
    document.getElementById('composeModal').style.display = 'block';
    
    // Set recipient type when recipient is selected
    document.getElementById('recipient').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        document.getElementById('recipient_type').value = selected.dataset.type;
    });
}

function hideComposeForm() {
    document.getElementById('composeModal').style.display = 'none';
}

function showMessage(message) {
    const modal = document.getElementById('viewModal');
    const content = document.getElementById('messageContent');
    
    content.innerHTML = `
        <div class="message-header">
            <h3>${message.subject}</h3>
            <div class="message-meta">
                From: ${message.sender_name || message.recipient_name}<br>
                Date: ${new Date(message.created_at).toLocaleString()}
            </div>
        </div>
        <div class="message-body">
            ${message.message}
        </div>
    `;
    
    modal.style.display = 'block';

    // Mark message as read if it's unread
    if (!message.read_at) {
        fetch('mark_message_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message_id: message.id
            })
        });
    }
}

function hideViewMessage() {
    document.getElementById('viewModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    const composeModal = document.getElementById('composeModal');
    const viewModal = document.getElementById('viewModal');
    if (event.target === composeModal) {
        composeModal.style.display = 'none';
    }
    if (event.target === viewModal) {
        viewModal.style.display = 'none';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?> 