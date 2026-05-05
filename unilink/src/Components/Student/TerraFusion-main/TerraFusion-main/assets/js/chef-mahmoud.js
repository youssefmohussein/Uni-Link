/**
 * Mahmoud AI Chatbot - TerraFusion Virtual Chef
 * Features: Personalized suggestions, direct cart add, reservations, Arabic/English support.
 * Adheres to User Prompt: Friendly, professional, clear, concise.
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Create UI Elements
    createChatUI();

    const chatToggle = document.getElementById('mahmoud-toggle');
    const chatWindow = document.getElementById('mahmoud-chat');
    const closeChat = document.getElementById('mahmoud-close');
    const chatInput = document.getElementById('mahmoud-input');
    const sendBtn = document.getElementById('mahmoud-send');
    const chatMessages = document.getElementById('mahmoud-messages');

    // 2. State & Data
    let isOpen = false;
    let reservationState = { active: false, step: 0, data: {} };
    let lastSuggestedCategory = null;
    let lastSuggestedMeal = null;
    const menuContext = window.terraMenu || {};
    const flatMenu = flattenMenu(menuContext);

    // Initial Greeting - REMOVED (Handled by loadState)

    // 3. Event Listeners
    chatToggle.addEventListener('click', toggleChat);
    closeChat.addEventListener('click', toggleChat);

    sendBtn.addEventListener('click', handleUserMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') handleUserMessage();
    });

    // --- Persistence Logic ---
    function saveState() {
        const state = {
            isOpen: isOpen,
            messages: chatMessages.innerHTML,
            reservation: reservationState
        };
        localStorage.setItem('mahmoudChatState', JSON.stringify(state));
    }

    function loadState() {
        const saved = localStorage.getItem('mahmoudChatState');
        if (saved) {
            const state = JSON.parse(saved);
            isOpen = state.isOpen;
            if (isOpen) {
                chatWindow.classList.add('active');
                chatToggle.classList.add('hidden');
            }
            if (state.messages) {
                chatMessages.innerHTML = state.messages;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
            if (state.reservation) {
                reservationState = state.reservation;
            }
        } else {
             // Initial Greeting only if no history
             setTimeout(() => {
                addMessage("Ahlan! I am Mahmoud, your virtual head chef. How can I help you today? / أهلاً بك! أنا محمود، رئيس الطهاة الافتراضي. كيف يمكنني مساعدتك اليوم؟", 'mahmoud');
            }, 1000);
        }
    }

    // Load state on startup
    loadState();

    // 4. Functions
    function toggleChat() {
        isOpen = !isOpen;
        chatWindow.classList.toggle('active', isOpen);
        chatToggle.classList.toggle('hidden', isOpen);
        saveState(); // Save open/closed state
    }

    function handleUserMessage() {
        const text = chatInput.value.trim();
        if (!text) return;

        addMessage(text, 'user');
        chatInput.value = '';

        // Determine language for response
        const isArabic = /[\u0600-\u06FF]/.test(text);

        // Process Mahmoud's response
        processQuery(text.toLowerCase(), isArabic);
    }

    function addMessage(text, sender) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `mahmoud-msg ${sender}-msg`;
        msgDiv.innerHTML = `<div class="msg-bubble">${text}</div>`;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        saveState(); // Save new message
    }

    async function processQuery(query, isArabic) {
        const typingId = showTyping(isArabic);

        try {
            const response = await fetch('chat_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: query,
                    menu: menuContext // Pass the menu context to Gemini
                })
            });

            const data = await response.json();
            removeTyping(typingId);

            if (data.error) {
                addMessage(isArabic ? "أعتذر، أواجه مشكلة حالياً. برجاء المحاولة لاحقاً." : "I apologize, I'm having a technical issue. Please try again later.", 'mahmoud');
                console.error("Mahmoud API Error:", data.error);
                return;
            }

            // 1. Display Text Reply
            if (data.reply) {
                addMessage(data.reply, 'mahmoud');
            }

            // 2. Handle Tool Calls (Function Calling)
            if (data.tool_calls && data.tool_calls.length > 0) {
                data.tool_calls.forEach(call => {
                    if (call.name === 'add_to_cart') {
                        const { meal_id, meal_name } = call.args;
                        if (typeof updateCart === 'function') {
                            updateCart('add', meal_id, 1).then(success => {
                                if (success) {
                                    addMessage(isArabic ? `لقد قمت بإضافة **${meal_name}** إلى سلتك! 🛒` : `I've added the **${meal_name}** to your cart! 🛒`, 'mahmoud');
                                }
                            });
                        }
                    }
                    if (call.name === 'create_reservation') {
                        const args = call.args;
                        fetch('save_reservation.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(args)
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    addMessage(isArabic ? `رائع! لقد سجلت حجزك ليوم **${args.reservation_date}** في تمام الساعة **${args.reservation_time}**. ننتظركم بشوق! 🍽️` : `Perfect! I've booked your table for **${args.reservation_date}** at **${args.reservation_time}**. We look forward to seeing you! 🍽️`, 'mahmoud');
                                } else {
                                    addMessage(isArabic ? "عذراً، حدث خطأ أثناء تسجيل الحجز. برجاء المحاولة لاحقاً." : "I apologize, something went wrong while booking your table. Please try again later.", 'mahmoud');
                                }
                            });
                    }
                });
            }

        } catch (error) {
            removeTyping(typingId);
            addMessage(isArabic ? "خطأ في الاتصال بالخادم." : "Error connecting to the chef's kitchen.", 'mahmoud');
            console.error("Mahmoud Connection Error:", error);
        }
    }

    function handleReservationFlow(query, isArabic) {
        switch (reservationState.step) {
            case 1:
                reservationState.data.date = query;
                reservationState.step = 2;
                addMessage(isArabic ? "رائع. كم عدد الأشخاص؟" : "Great. For how many people?", 'mahmoud');
                break;
            case 2:
                reservationState.data.people = query;
                reservationState.step = 3;
                addMessage(isArabic ? "ما هو الوقت المفضل؟" : "What time would you prefer?", 'mahmoud');
                break;
            case 3:
                reservationState.data.time = query;
                reservationState.active = false;
                reservationState.step = 0;
                addMessage(isArabic ?
                    `لقد سجلت تفاصيل حجزك: ${reservationState.data.date} لـ ${reservationState.data.people} أشخاص في تمام الساعة ${reservationState.data.time}. سيتصل بك فريقنا قريباً للتأكيد!` :
                    `I've noted your reservation details: ${reservationState.data.date} for ${reservationState.data.people} people at ${reservationState.data.time}. Our team will contact you shortly to confirm!`,
                    'mahmoud');
                break;
        }
        saveState(); // Save reservation progress
    }

    function findMeal(query) {
        return flatMenu.find(m => query.includes(m.meal_name.toLowerCase()));
    }

    function flattenMenu(categories) {
        let all = [];
        Object.values(categories).forEach(cat => {
            all = all.concat(cat);
        });
        return all;
    }

    function showTyping(isArabic) {
        const id = 'typing-' + Date.now();
        const typingDiv = document.createElement('div');
        typingDiv.id = id;
        typingDiv.className = 'mahmoud-msg mahmoud-msg-typing';
        typingDiv.innerHTML = `<div class="msg-bubble">${isArabic ? "محمود يفكر..." : "Mahmoud is thinking..."}</div>`;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return id;
    }

    function removeTyping(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    // --- Add Clear Chat Button to Header ---
    function createChatUI() {
        // ... (existing HTML remains same, we will append the proactive bubble dynamically)
        const html = `
            <div id="mahmoud-widget">
                <div id="mahmoud-greeting-container"></div>
                <button id="mahmoud-toggle">
                    <img src="images/ai-mahmoud.png" alt="Chef Mahmoud">
                    <span class="pulse"></span>
                </button>
                
                <div id="mahmoud-chat">
                    <div class="mahmoud-header">
                        <div class="header-info">
                            <img src="images/ai-mahmoud.png" alt="Chef Mahmoud">
                            <div>
                                <h4>Chef Mahmoud</h4>
                                <span class="status">Online</span>
                            </div>
                        </div>
                        <div class="header-actions">
                            <button id="mahmoud-clear" title="Clear Chat" style="background:none;border:none;color:var(--text-secondary);margin-right:8px;"><i class="bi bi-trash"></i></button>
                            <button id="mahmoud-close"><i class="bi bi-x"></i></button>
                        </div>
                    </div>
                    
                    <div id="mahmoud-messages"></div>
                    
                    <div class="mahmoud-footer">
                        <input type="text" id="mahmoud-input" placeholder="Type to Mahmoud...">
                        <button id="mahmoud-send"><i class="bi bi-send-fill"></i></button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);
        
        // Add clear listener
        document.getElementById('mahmoud-clear').addEventListener('click', () => {
             localStorage.removeItem('mahmoudChatState');
             document.getElementById('mahmoud-messages').innerHTML = '';
             isOpen = false;
             loadState(); // Re-init
        });

        // Trigger proactive greeting if on index.php
        const currentUrl = window.location.href.toLowerCase();
        const isHomePage = currentUrl.endsWith('index.php') || currentUrl.endsWith('terrafusion/') || currentUrl.endsWith('terrafusion') || !currentUrl.includes('.php');

        if (isHomePage) {
            console.log("Mahmoud: Home page detected.");
            setTimeout(() => {
                if (typeof showProactiveGreeting === 'function') showProactiveGreeting();
            }, 2500);
        }
    }

    function showProactiveGreeting() {
        const container = document.getElementById('mahmoud-greeting-container');
        const toggle = document.getElementById('mahmoud-toggle');

        if (!container || !toggle) return;

        // Check if user has already interacted/closed, maybe don't show annoying popup? 
        // For now, keep as is.
        console.log("Mahmoud: Starting proactive greeting...");
        const text = "Hi! I'm Chef Mahmoud. Need help?";
        const bubble = document.createElement('div');
        bubble.className = 'mahmoud-proactive-bubble';
        bubble.innerHTML = text;
        container.appendChild(bubble);

        // Final check to make sure classes are added after a small browser paint cycle
        requestAnimationFrame(() => {
            setTimeout(() => {
                toggle.classList.add('proactive');
                bubble.classList.add('show');
            }, 50);
        });

        // Retract after 5.5 seconds
        setTimeout(() => {
            bubble.classList.remove('show');
            toggle.classList.remove('proactive');
            setTimeout(() => {
                if (bubble.parentNode) bubble.remove();
            }, 600);
        }, 5500);
    }
});
