
const container = document.getElementById("messageContainer");
const chatId    = document.getElementById("chatId").value;
let firstTime   = true;
let allLoaded   = false;

const EMOJIS = ['👍', '❤️', '😂', '😮', '😢', '😡'];


container.addEventListener("scroll", () => {
    if (container.scrollTop <= 10 && !allLoaded) {
        const input = document.getElementById("loadCount");
        input.value = parseInt(input.value, 10) + 1;
    }
});


document.getElementById("chatForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const msgInput = document.getElementById("message");
    if (msgInput.value.length === 0) return;

    fetch("../src/api/sendMessage.php", {
        method: "POST",
        headers: { "Content-type": "application/json" },
        body: JSON.stringify({ message: msgInput.value, cid: chatId })
    })
        .then(res => res.json())
        .then(data => console.log(data));

    msgInput.value = "";
});


function showEmojiPicker(e, messageId) {
    e.stopPropagation();
    document.querySelectorAll(".emoji-picker").forEach(p => p.remove());

    const rect   = e.target.getBoundingClientRect();
    const picker = document.createElement("div");
    picker.classList.add("emoji-picker");
    picker.style.top  = (rect.top - 48 + window.scrollY) + "px";
    picker.style.left = rect.left + "px";

    EMOJIS.forEach(emoji => {
        const btn = document.createElement("button");
        btn.textContent = emoji;
        btn.addEventListener("click", (ev) => {
            ev.stopPropagation();
            toggleReaction(messageId, emoji);
            picker.remove();
        });
        picker.appendChild(btn);
    });

    document.body.appendChild(picker);
    setTimeout(() => {
        document.addEventListener("click", () => picker.remove(), { once: true });
    }, 0);
}


function toggleReaction(messageId, emoji) {
    fetch("../src/api/toggleReaction.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mid: messageId, emoji })
    })
    .then(res => res.json())
    .then(data => console.log(data));
}


function loadMessages() {
    fetch("../src/api/getMessage.php?cid=" + chatId
        + "&loadCount=" + document.getElementById("loadCount").value)
    .then(res => res.json())
    .then((data) => {
        container.replaceChildren();

        data['messages'].forEach(element => {
            const isMine = element.sender_id == document.getElementById("uid_reference").value;

            const row = document.createElement("div");
            row.classList.add("msg-row", isMine ? "right" : "left");

            const bubble = document.createElement("div");
            bubble.classList.add("msg-bubble", isMine ? "sent" : "received");

            if (!isMine) {
                const sender = document.createElement("div");
                sender.classList.add("msg-sender");
                sender.textContent = element.sender_name;
                bubble.appendChild(sender);
            }

            const content = document.createElement("div");
            content.textContent = element.content;

            const time = document.createElement("div");
            time.classList.add("msg-time");
            time.textContent = element.sent_at;

            bubble.appendChild(content);
            bubble.appendChild(time);

            const reactBar = document.createElement("div");
            reactBar.classList.add("react-bar");

            (element.reactions || []).forEach(r => {
                const pill = document.createElement("button");
                pill.classList.add("react-pill");
                if (r.user_reacted) pill.classList.add("reacted");
                pill.textContent = `${r.emoji} ${r.count}`;
                pill.addEventListener("click", () => toggleReaction(element.id, r.emoji));
                reactBar.appendChild(pill);
            });

            const addBtn = document.createElement("button");
            addBtn.classList.add("react-add");
            addBtn.textContent = "+";
            addBtn.addEventListener("click", (e) => showEmojiPicker(e, element.id));
            reactBar.appendChild(addBtn);

            bubble.appendChild(reactBar);
            row.appendChild(bubble);
            container.appendChild(row);
        });

        if (firstTime) {
            container.scrollTop = container.scrollHeight;
            firstTime = false;
        }

        if (data.limit >= data.total) allLoaded = true;

        if (data.messages && data.messages.length > 0) {
            const latest = data.messages[data.messages.length - 1].sent_at;
            localStorage.setItem("chat_read_" + chatId, latest);
        }
    });
}


function manageMember(cid, uid, action) {
    const messages = {
        kick:    "Are you sure you want to kick this member?",
        promote: "Make this member an admin?",
        demote:  "Remove admin rights from this member?",
    };
    if (!confirm(messages[action])) return;

    fetch("../src/api/manageMember.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cid, uid, action })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "ok") location.reload();
        else alert("Error: " + data.message);
    });
}


setInterval(loadMessages, 2000);
