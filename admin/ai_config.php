<?php 
include 'includes/check_role.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Trợ Lý Quản Trị | Vanhoc247 Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --netflix-black: #141414;
            --chat-bg: #0a0a0c;
            --msg-user: #202023;
            --muse-gradient: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            --border-color: rgba(255, 255, 255, 0.08);
        }
        
        body { background-color: var(--netflix-black); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; }
        .main-content { margin-left: 280px; height: 100vh; display: flex; flex-direction: column; }
        
        /* Header Bar */
        .admin-header {
            padding: 20px 4%; z-index: 100;
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(0,0,0,0.8);
            border-bottom: 1px solid var(--border-color);
        }

        /* KHUNG CHAT CHÍNH */
        .chat-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--chat-bg);
            position: relative;
            overflow: hidden;
        }

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 30px 10% 120px 10%;
        }

        .message-row { margin-bottom: 30px; display: flex; gap: 20px; animation: fadeIn 0.3s ease-in; }
        .message-row.user-msg { justify-content: flex-end; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .avatar-ai {
            width: 38px; height: 38px;
            background: var(--muse-gradient);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(245, 87, 108, 0.3);
        }
        .avatar-user {
            width: 38px; height: 38px;
            background: #4facfe;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; flex-shrink: 0;
            font-weight: bold;
        }

        .message-content {
            max-width: 80%;
            line-height: 1.6;
            color: #e0e0e0;
            font-size: 0.95rem;
            word-wrap: break-word;
        }
        .user-msg .message-content {
            background: var(--msg-user);
            padding: 12px 18px;
            border-radius: 15px 15px 0 15px;
            border: 1px solid var(--border-color);
        }
        .ai-msg .message-content {
            padding: 5px 0;
        }

        /* Ô NHẬP LIỆU (INPUT) */
        .chat-input-area {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 70%;
            background: #1e1e20;
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            z-index: 10;
        }

        .chat-input-area input {
            background: transparent;
            border: none;
            color: #fff;
            flex-grow: 1;
            padding: 10px;
            outline: none;
            font-size: 0.95rem;
        }

        .send-btn {
            background: var(--muse-gradient);
            border: none;
            width: 38px; height: 38px;
            border-radius: 10px;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s;
        }
        .send-btn:hover { transform: scale(1.1); box-shadow: 0 0 15px rgba(245, 87, 108, 0.4); }
        .send-btn:disabled { filter: grayscale(1); cursor: not-allowed; transform: none; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    
    <div class="admin-header">
        <div>
            <h5 class="fw-bold m-0"><i class="fas fa-robot text-warning me-2"></i> Trợ Lý AI (Gemini)</h5>
            <small class="text-white-50">Tạo đề cương, giải đáp văn học, hỗ trợ quản trị nội dung tự động</small>
        </div>
    </div>

    <!-- MAIN CHAT -->
    <main class="chat-main">
        <div class="chat-messages" id="chatWindow">
            <!-- Tin nhắn mặc định của AI -->
            <div class="message-row ai-msg">
                <div class="avatar-ai"><i class="fas fa-sparkles"></i></div>
                <div class="message-content">
                    Chào Admin! Tôi là Trợ giảng AI thông minh được tích hợp trực tiếp từ Google Gemini. <br>
                    Tôi có thể giúp bạn soạn thảo giáo án chi tiết, xây dựng ngân hàng bài tập chuyên sâu, và quản trị nội dung học thuật chất lượng cao cho học sinh. Hôm nay chúng ta sẽ bắt đầu với chủ đề gì nào? 🎓
                </div>
            </div>
        </div>

        <!-- INPUT AREA -->
        <div class="chat-input-area">
            <i class="fas fa-magic text-warning ms-2"></i>
            <input type="text" id="userInput" placeholder="Nhắn tin với AI (VD: Viết cho tôi bài phân tích nhân vật Mị mở rộng)..." autocomplete="off">
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

    // Hàm format Text (Chuyển \n thành thẻ <br>, và bôi đậm text)
    function formatText(text) {
        let formatted = text.replace(/\n/g, '<br>');
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        return formatted;
    }

    // Hàm render tin nhắn ra UI
    function appendMessage(sender, text, isHtml = false) {
        const row = document.createElement('div');
        row.className = 'message-row ' + (sender === 'user' ? 'user-msg' : 'ai-msg');
        
        let displayContent = isHtml ? text : formatText(text);

        if (sender === 'user') {
            row.innerHTML = `
                <div class="message-content">${displayContent}</div>
                <div class="avatar-user">AD</div>
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

        // Vô hiệu hóa input trong lúc đợi
        input.value = "";
        input.disabled = true;
        sendBtn.disabled = true;

        // In messsage User
        appendMessage('user', msg);
        
        // In trạng thái Loading của AI
        const loadingRow = appendMessage('ai', '<span class="text-white-50"><i class="fas fa-spinner fa-spin me-2"></i>AI đang suy nghĩ...</span>', true);

        // Chuẩn bị Data gửi tới api_ai.php
        const formData = new URLSearchParams();
        formData.append('message', msg);

        try {
            const response = await fetch('../api_ai.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            });

            const result = await response.json();
            
            // Xóa dòng loading
            loadingRow.remove();

            if (result.status === 'success') {
                appendMessage('ai', result.reply);
            } else {
                appendMessage('ai', `<span class="text-danger">Lỗi: ${result.reply}</span>`, true);
            }
        } catch (error) {
            loadingRow.remove();
            appendMessage('ai', `<span class="text-danger">Lỗi kết nối API. Vui lòng thử lại!</span>`, true);
            console.error("API Error:", error);
        } finally {
            // Mở lại input
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    sendBtn.addEventListener('click', sendMessage);
    
    // Hỗ trợ ấn Enter để gửi
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
