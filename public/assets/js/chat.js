
const container = document.getElementById("messageContainer");
let firstTime = true;
let allLoaded = false; 


container.addEventListener("scroll", () => {
    if (container.scrollTop <= 10 && !allLoaded) {
        isLoading = true;

        const input = document.getElementById("loadCount");
        input.value = parseInt(input.value, 10) + 1;

       
    }
});


document.getElementById("chatForm").addEventListener("submit", (e) => {
    e.preventDefault();
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
         
    })
        .then(res => res.text())
        .then(data => console.log(data));
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



function loadMessages(){
    fetch("../src/api/getMessage.php?cid=" 
        + document.getElementById("chatId").value 
        + "&loadCount=" + document.getElementById("loadCount").value)
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

        if(firstTime){
            container.scrollTop = container.scrollHeight;
            firstTime = false;
        }

        if (data.limit >= data.total) {
            allLoaded = true;
        }
    });
}

setInterval(loadMessages,2000);

