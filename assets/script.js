
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp'); // Default animation
                    // Add specific animations based on classes if needed
                    if (entry.target.classList.contains('animate__fadeInLeft')) {
                        entry.target.classList.add('animate__fadeInLeft');
                    } else if (entry.target.classList.contains('animate__fadeInRight')) {
                        entry.target.classList.add('animate__fadeInRight');
                    } else if (entry.target.classList.contains('animate__zoomIn')) {
                        entry.target.classList.add('animate__zoomIn');
                    } else if (entry.target.classList.contains('animate__bounceIn')) {
                        entry.target.classList.add('animate__bounceIn');
                    } else if (entry.target.classList.contains('animate__fadeInDown')) {
                        entry.target.classList.add('animate__fadeInDown');
                    }
                    entry.target.style.visibility = 'visible';
                    observer.unobserve(entry.target); // Stop observing once animated
                }
            });
        }, {
            threshold: 0.1 // Trigger when 10% of the element is visible
        });

        document.querySelectorAll('.animate__animated').forEach(element => {
            observer.observe(element);
        });
    });
document.getElementById('send-btn').addEventListener('click', function() {
    sendMessage();
});

// Giả lập chatbot Cho phép nhấn Enter để gửi
document.getElementById('user-input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') { sendMessage(); }
});

function sendMessage() {
    const input = document.getElementById('user-input');
    const chatBox = document.getElementById('chat-box');
    const message = input.value.trim();

    if (message !== "") {
        // 1. Thêm tin nhắn của User vào khung chat
        const userDiv = document.createElement('div');
        userDiv.className = 'd-flex justify-content-end mb-4';
        userDiv.innerHTML = `<div class="bg-primary text-white p-3 rounded-4 rounded-end-0 shadow-sm" style="max-width: 80%;">${message}</div>`;
        chatBox.appendChild(userDiv);

        // 2. Clear ô input
        input.value = "";

        // 3. Cuộn xuống dưới cùng của khung chat
        chatBox.scrollTop = chatBox.scrollHeight;

        // 4. Giả lập Bot đang trả lời (sau 1 giây)
        setTimeout(() => {
            const botDiv = document.createElement('div');
            botDiv.className = 'd-flex mb-4';
            botDiv.innerHTML = `<div class="bg-light p-3 rounded-4 rounded-start-0 shadow-sm border-start border-primary border-4" style="max-width: 80%;">
                                    Đây là câu trả lời mẫu từ Vanhoc247 AI cho: <strong>${message}</strong>. 
                                    (Sau này bạn sẽ thay bằng dữ liệu thực từ API)
                                </div>`;
            chatBox.appendChild(botDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 1000);
    }
}
