
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
        fetch("../src/api/getMessage.php?cid="+ document.getElementById("chatId").value + "&loadCount="+ document.getElementById("loadCount").value)
        .then(res=>res.json())
        .then(
            (data) => {
                container.replaceChildren();
                data['messages'].forEach(element => {
                        const message = document.createElement("div");
                        const content = document.createElement("p");
                        const time = document.createElement("p");
                        content.textContent = element.content;
                        time.textContent = element.sent_at;
                        if(element.sender_id ==  document.getElementById("uid_reference").value){
                            message.style.display = "flex";
                            message.style.justifyContent = "flex-end";
                        }
                        message.appendChild(content);
                        message.appendChild(time);
                        container.appendChild(message);
                });
                if(firstTime){
                    container.scrollTop = container.scrollHeight;
                    firstTime = false;
                }
                 if (data.limit >= data.total) {
                    allLoaded = true;
                    console.log("All messages loaded");
                }

            }
        )
}

setInterval(loadMessages,2000);

