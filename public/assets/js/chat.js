
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
<<<<<<< HEAD
    console.log(document.getElementById("chatId").value);
    if(document.getElementById("message").value.length != 0){
    fetch("../src/api/sendMessage.php",{
        method:"POST",
        headers: {
            "Content-type":"application/json"
        },
        body: JSON.stringify({
            message: document.getElementById("message").value,
            cid: document.getElementById("chatId").value,
            attachedPicture: document.getElementById("fileInput").files[0]?.name || ''
        })
         
=======
    const msgInput = document.getElementById("message");
    if (msgInput.value.length === 0) return;

    fetch("../src/api/sendMessage.php", {
        method: "POST",
        headers: { "Content-type": "application/json" },
        body: JSON.stringify({ message: msgInput.value, cid: chatId })
>>>>>>> groups
    })
        .then(res => res.text())
        .then(data => console.log(data));
<<<<<<< HEAD
        document.getElementById("message").value = "";

    const fileInput = document.getElementById("fileInput");
    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append("picture", file);    
    fetch("../src/api/uploadPicture.php",{
        method:"POST",
        body: formData
         
    }).then(response => response.text())   // or response.json() if PHP returns JSON
    .then(data => {
        console.log("Server response:", data);
    })
    .catch(error => {
        console.error("Upload error:", error);
    });  
    }
});   
=======

    msgInput.value = "";
});
>>>>>>> groups


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
<<<<<<< HEAD
        .then(res => res.json())
        .then((data) => {

            container.replaceChildren();

        data['messages'].forEach(element => {

        const isMine = element.sender_id == document.getElementById("uid_reference").value;

        // row
        const row = document.createElement("div");
        row.classList.add("msg-row", isMine ? "right" : "left");

        // avatar container
        const avatar = document.createElement("img");
        avatar.classList.add("msg-avatar");

        avatar.src = element.profilePicturePath
            ? element.profilePicturePath
            : "./assets/img/anonymous.png";

        avatar.onerror = () => {
            avatar.src = "./assets/img/anonymous.png";
        };

        // bubble
        const bubble = document.createElement("div");
        bubble.classList.add("msg-bubble", isMine ? "sent" : "received");

        // message text
        const content = document.createElement("div");
        content.textContent = element.content;

        // time
        const time = document.createElement("div");
        time.classList.add("msg-time");
        time.textContent = element.sent_at;
        if (element.picturePath) {
            const img = document.createElement("img");
            img.classList.add("msg-image");
            img.src = "./assets/img/chats/" + element.picturePath;

            img.onerror = () => {
                img.remove(); // or set fallback image if you want
            };

            bubble.appendChild(img);
        }
        bubble.appendChild(content);
        bubble.appendChild(time);
        const editBtn = document.createElement("button");
        editBtn.classList.add("msg-edit");
        editBtn.textContent = "Edit";
        // Delete/edit button
        const actions = document.createElement("div");
        actions.classList.add("msg-actions");
        // Delete button
        const deleteBtn = document.createElement("button");
        deleteBtn.classList.add("msg-delete");
        deleteBtn.textContent = "Delete";

                // (optional hooks)
        editBtn.onclick = () => {

            const newMessage = prompt("Edit your message:", element.content);

            if (newMessage === null) {
                return;
            }

            if (newMessage.trim().length === 0) {
                alert("Message cannot be empty");
                return;
            }

            fetch("../src/api/updateMessage.php", {
                method: "POST",
                headers: {
                    "Content-type": "application/json"
                },
                body: JSON.stringify({
                    message_id: element.message_id,
                    message: newMessage
                })
            })
            .then(res => res.json())
            .then(data => {

                console.log(data);

                if (data.status === "OK") {
                    loadMessages();
                } else {
                    alert(data.message);
                }

            })
            .catch(err => console.log(err));
        };

        deleteBtn.onclick = () => {
            console.log(element);
            fetch("../src/api/deleteMessage.php?id="+element.message_id).then(response => console.log(response));
            console.log(response);

        //     .then(()=>{
        //                     location.reload();

        //     });
        };


        // order depends on left/right alignment
        if (isMine) {
=======
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
>>>>>>> groups
            row.appendChild(bubble);
            row.appendChild(avatar);
            actions.appendChild(editBtn);
            actions.appendChild(deleteBtn);
            bubble.appendChild(actions);
        } else {
            row.appendChild(avatar);
            row.appendChild(bubble);
        }
        

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


function setNickname(cid, uid, current) {
    const input = prompt("Set nickname (leave blank to clear):", current);
    if (input === null) return;

    fetch("../src/api/setNickname.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cid, uid, nickname: input.trim() })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "ok") location.reload();
        else alert("Error: " + data.message);
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
