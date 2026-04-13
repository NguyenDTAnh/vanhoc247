<?php 
include 'includes/header.php'; 
// Giả định mày đã có session từ header
?>

<style>
    :root {
        --chat-bg: #0a0a0c;
        --sidebar-bg: #121214;
        --msg-user: #202023;
        --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
        --border-color: rgba(255, 255, 255, 0.08);
    }

    body { background-color: var(--chat-bg); overflow: hidden; }

    .chat-container {
        display: flex;
        height: calc(100vh - 75px); /* Trừ đi chiều cao header */
        margin-top: 0;
    }

    /* SIDEBAR LỊCH SỬ CHAT */
    .chat-sidebar {
        width: 280px;
        background-color: var(--sidebar-bg);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        padding: 15px;
    }

    .new-chat-btn {
        border: 1px solid var(--border-color);
        background: transparent;
        color: #fff;
        padding: 10px;
        border-radius: 10px;
        width: 100%;
        margin-bottom: 20px;
        transition: 0.3s;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .new-chat-btn:hover {
        background: rgba(255,255,255,0.05);
        border-color: #f5576c;
    }

    .chat-history-list {
        flex-grow: 1;
        overflow-y: auto;
    }
    .history-item {
        padding: 10px;
        border-radius: 8px;
        color: #888;
        font-size: 0.85rem;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 5px;
    }
    .history-item:hover { background: rgba(255,255,255,0.03); color: #fff; }

    /* KHUNG CHAT CHÍNH */
    .chat-main {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        background-color: var(--chat-bg);
        position: relative;
    }

    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 40px 15% 100px 15%; /* Căn giữa nội dung chat */
    }

    .message-row { margin-bottom: 30px; display: flex; gap: 20px; }
    .message-row.user { justify-content: flex-end; }

    .avatar-ai {
        width: 35px; height: 35px;
        background: var(--muse-gradient);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; flex-shrink: 0;
    }
    .avatar-user {
        width: 35px; height: 35px;
        background: #444;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; flex-shrink: 0;
    }

    .message-content {
        max-width: 80%;
        line-height: 1.6;
        color: #e0e0e0;
        font-size: 0.95rem;
    }
    .user .message-content {
        background: var(--msg-user);
        padding: 12px 18px;
        border-radius: 15px;
    }

    /* Ô NHẬP LIỆU (INPUT) */
    .chat-input-area {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        background: #1e1e20;
        border: 1px solid var(--border-color);
        border-radius: 15px;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .chat-input-area input {
        background: transparent;
        border: none;
        color: #fff;
        flex-grow: 1;
        padding: 10px;
        outline: none;
    }

    .send-btn {
        background: var(--muse-gradient);
        border: none;
        width: 35px; height: 35px;
        border-radius: 10px;
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        transition: 0.3s;
    }
    .send-btn:hover { transform: scale(1.1); }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }

    @media (max-width: 992px) {
        .chat-sidebar { display: none; }
        .chat-messages { padding: 20px 5%; }
        .chat-input-area { width: 90%; }
    }
</style>

<div class="chat-container">
    <!-- SIDEBAR: LỊCH SỬ -->
    <aside class="chat-sidebar">
        <button class="new-chat-btn">
            <i class="fas fa-plus"></i> Cuộc hội thoại mới
        </button>
        
        <p class="text-white-25 small text-uppercase fw-bold mb-3" style="font-size: 0.7rem;">Gần đây</p>
        <div class="chat-history-list">
            <div class="history-item"><i class="far fa-comment-alt me-2"></i> Phân tích bài thơ Sóng</div>
            <div class="history-item"><i class="far fa-comment-alt me-2"></i> Dàn ý Nghị luận xã hội</div>
            <div class="history-item"><i class="far fa-comment-alt me-2"></i> Kiểm tra kiến thức 12</div>
        </div>

        <div class="mt-auto pt-3 border-top border-secondary opacity-50">
            <div class="d-flex align-items-center gap-2 text-white-50 small">
                <i class="fas fa-crown text-warning"></i>
                <span>Muse Pro Plan</span>
            </div>
        </div>
    </aside>

    <!-- MAIN CHAT -->
    <main class="chat-main">
        <div class="chat-messages" id="chatWindow">
            <!-- Tin nhắn của AI -->
            <div class="message-row">
                <div class="avatar-ai"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    Chào <strong><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'bạn'; ?></strong>! 
                    Tôi là Trợ lý Muse AI. Bạn cần tôi hỗ trợ gì về môn Văn hôm nay? 
                    Tôi có thể giúp bạn lập dàn ý, phân tích tác phẩm hoặc sửa lỗi diễn đạt. ✨
                </div>
            </div>

            <!-- Tin nhắn của User -->
            <div class="message-row user">
                <div class="message-content">
                    Giúp mình mở bài bài thơ Tây Tiến của Quang Dũng thật ấn tượng.
                </div>
                <div class="avatar-user">
                    <?php echo isset($_SESSION['username']) ? strtoupper(substr($_SESSION['username'], 0, 1)) : 'U'; ?>
                </div>
            </div>

            <!-- Phản hồi của AI -->
            <div class="message-row">
                <div class="avatar-ai"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    Đây là một gợi ý mở bài gián tiếp dành cho bạn: <br><br>
                    <i>"Có những vùng đất ta đi qua chỉ là nơi ở, nhưng có những mảnh tâm hồn đã hóa thành thơ. Tây Tiến của Quang Dũng chính là một mảnh tâm hồn như thế. Bản hùng ca về binh đoàn Tây Tiến không chỉ tái hiện một thời kỳ gian khổ nhưng hào hùng của dân tộc, mà còn là bức tranh tượng đài bất tử về người lính trí thức Hà thành..."</i>
                </div>
            </div>
        </div>

        <!-- INPUT AREA -->
        <div class="chat-input-area">
            <i class="far fa-image text-white-50 cursor-pointer"></i>
            <input type="text" id="userInput" placeholder="Hỏi Muse AI bất cứ điều gì về văn học...">
            <button class="send-btn" id="sendBtn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </main>
</div>

<script>
    // Một chút JS để cuộn xuống cuối khi có tin nhắn mới
    const chatWindow = document.getElementById('chatWindow');
    chatWindow.scrollTop = chatWindow.scrollHeight;

    // Xử lý gửi tin nhắn (Giao diện giả lập)
    document.getElementById('sendBtn').addEventListener('click', function() {
        const input = document.getElementById('userInput');
        if(input.value.trim() !== "") {
            // Thêm tin nhắn user vào giao diện...
            input.value = "";
        }
    });
</script>

<?php include 'includes/footer.php'; ?>