/**
 * IdeaSync Premium Platform JS
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log('IdeaSync Engine Initialized // 2026');

    // Real-time Polling for Notifications & Messages
    const pollRealtime = async () => {
        const toId = new URLSearchParams(window.location.search).get('to') || 0;
        const chatBox = document.getElementById('chat-messages');
        const lastMsgId = chatBox ? chatBox.dataset.lastId || 0 : 0;

        try {
            const res = await fetch(`${BASE_URL}/src/controllers/realtime.php?to=${toId}&last_id=${lastMsgId}`);
            const data = await res.json();

            if (data.success) {
                // Update Notification Dots
                const dots = document.querySelectorAll('.notif-dot');
                dots.forEach(dot => {
                    if (data.unread_notifications > 0) dot.classList.remove('hidden');
                    else dot.classList.add('hidden');
                });

                // Update Active Chat
                if (data.new_messages && data.new_messages.length > 0 && chatBox) {
                    const currentUserId = chatBox.dataset.userId;
                    data.new_messages.forEach(m => {
                        const isMe = m.sender_user_id == currentUserId;
                        const msgDiv = document.createElement('div');
                        msgDiv.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
                        msgDiv.innerHTML = `
                            <div class="max-w-[70%] p-4 rounded-2xl text-sm font-medium shadow-subtle ${isMe ? 'bg-primary text-white' : 'bg-white text-slate-700 border border-slate-100'}">
                                ${m.message}
                                <div class="flex items-center justify-between mt-2 gap-4">
                                    <p class="text-[9px] opacity-60 font-bold uppercase">${new Date(m.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                                </div>
                            </div>
                        `;
                        chatBox.appendChild(msgDiv);
                        chatBox.dataset.lastId = m.id;
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            }
        } catch (e) {}
    };

    if (typeof LOGGED_IN !== 'undefined' && LOGGED_IN) {
        setInterval(pollRealtime, 3000); // Poll every 3 seconds
    }

    // Intersection Observer for fade-up animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-up-active');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-fade-up').forEach(el => {
        observer.observe(el);
    });
});

// Utility for formatting numbers
function formatImpact(num) {
    if (num >= 1000) return (num / 1000).toFixed(1) + 'k';
    return num;
}

// GSAP Animations
if (typeof gsap !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);

    // Reveal sections on scroll
    gsap.utils.toArray('.premium-card').forEach(card => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: "top 90%",
                toggleActions: "play none none none"
            },
            y: 30,
            opacity: 0,
            duration: 1,
            ease: "expo.out"
        });
    });

    // Hero title split animation (simulated)
    gsap.from("h1", {
        opacity: 0,
        y: 50,
        duration: 1.5,
        ease: "power4.out",
        stagger: 0.2
    });
}
