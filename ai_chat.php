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
            <!-- Lời chào ban đầu của Giáo viên AI -->
            <div class="message-row">
                <div class="avatar-ai"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    Chào em, <strong><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'học sinh thân mến'; ?></strong>! <br><br>
                    Thầy/Cô là Trợ lý Giáo viên AI của Vanhoc247. Em cần hỗ trợ giảng giải về tác phẩm nào, gợi ý lập dàn ý, hay cần chấm thử bài viết phân tích văn học ngày hôm nay không? Cứ thoải mái hỏi nhé! 📚✨
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
    const chatWindow = document.getElementById('chatWindow');
    const input = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');
    
    // Cuộn xuống cuối dải chat khi load
    chatWindow.scrollTop = chatWindow.scrollHeight;

    // Hàm format Text (Chuyển \n thành thẻ <br>, và bôi đậm text)
    function formatText(text) {
        let formatted = text.replace(/\n/g, '<br>');
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        return formatted;
    }

    // Hàm render tin nhắn ra UI (Giống form của file admin/ai_config.php)
    function appendMessage(sender, text, isHtml = false) {
        const row = document.createElement('div');
        row.className = 'message-row ' + (sender === 'user' ? 'user' : '');
        
        let displayContent = isHtml ? text : formatText(text);

        if (sender === 'user') {
            // Lấy ký tự đầu của User nếu có session, ở đây fix tạm là U
            row.innerHTML = `
                <div class="message-content">${displayContent}</div>
                <div class="avatar-user">U</div>
            `;
        } else {
            row.innerHTML = `
                <div class="avatar-ai"><i class="fas fa-robot"></i></div>
                <div class="message-content">${displayContent}</div>
            `;
        }
        
        chatWindow.appendChild(row);
        chatWindow.scrollTop = chatWindow.scrollHeight;
        return row; 
    }

    async function sendMessage() {
        const msg = input.value.trim();
        if(!msg) return;

        // Tạm khóa ô text để tránh user spam F5 hay click nhiều lần
        input.value = "";
        input.disabled = true;
        sendBtn.disabled = true;

        // 1. Hiện tin nhắn của User
        appendMessage('user', msg);
        
        // 2. Hiện hiệu ứng chờ của AI
        const loadingRow = appendMessage('ai', '<span class="text-white-50"><i class="fas fa-spinner fa-spin me-2"></i>Muse đang suy nghĩ...</span>', true);

        // 3. Chuẩn bị payload gửi cho api_ai.php ở cùng thư mục
        const formData = new URLSearchParams();
        formData.append('message', msg);

        try {
            const response = await fetch('api_ai.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            });

            const result = await response.json();
            
            // Xóa cái dòng "Đang suy nghĩ..." đi
            loadingRow.remove();

            // 4. In kết quả AI trả về
            if (result.status === 'success') {
                appendMessage('ai', result.reply);
            } else {
                appendMessage('ai', `<span class="text-danger">Lỗi: ${result.reply}</span>`, true);
            }
        } catch (error) {
            loadingRow.remove();
            appendMessage('ai', `<span class="text-danger">Kho dữ liệu Tàng Kinh Các đang bảo trì hoặc mất mạng. Cấp báo!</span>`, true);
            console.error("API Error:", error);
        } finally {
            // Nhả khóa form
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    // Bind nút Click
    sendBtn.addEventListener('click', sendMessage);
    
    // Bind phím Enter
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>