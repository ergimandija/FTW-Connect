
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
            cid: document.getElementById("chatId").value
        })
         
    })
        .then(res => res.json())
        .then(data => console.log(data));
        document.getElementById("message").value = "";
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

            bubble.appendChild(content);
            bubble.appendChild(time);
            row.appendChild(bubble);
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

