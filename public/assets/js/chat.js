document.getElementById("chatForm").addEventListener("submit", (e) => {
    e.preventDefault();
    console.log(document.getElementById("chatId").value);
    if(document.getElementById("message").value.length != 0)
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

});   


   setInterval(() =>        
        fetch("../src/api/getMessage.php?cid="+ document.getElementById("chatId").value)
        .then(res=>res.json())
        .then(
            (data) => {
                const container = document.getElementById("messageContainer");
                container.replaceChildren();
                data.forEach(element => {
                        const message = document.createElement("div");
                        const content = document.createElement("p");
                        const time = document.createElement("p");
                        content.textContent = element.content;
                        time.textContent = element.sent_at;
                        if(element.sender_id ==  document.getElementById("uid_reference").value){
                            
                            message.style.display = "flex";
                            message.style.flexDirection = "column";
                            message.style.marginLeft = "auto";
                        }

                        message.appendChild(content);
                        message.appendChild(time);
                        container.appendChild(message);
                });

            }
        ),2000);

