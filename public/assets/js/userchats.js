

function searchChat(){
    let searchValue =  document.getElementById('search-input').value;
    const userList = document.getElementById('userList-container');
    const listElements = userList.querySelectorAll('div');
    console.log(listElements);
    listElements.forEach(element => {
        console.log(element.textContent);
       if (element.textContent.toLowerCase().includes(searchValue.toLowerCase())) {
            element.classList.remove('d-none');
            element.classList.add('d-flex');
        } else {
            element.classList.remove('d-flex');
            element.classList.add('d-none');
        }
    });

}

function showContainer(containerId) {
    document.querySelectorAll("#userList-container, #archive-container")
        .forEach(el => el.classList.add("d-none"));

    document.getElementById(containerId).classList.remove("d-none");
}

function archiveChat(chatId){
        const resp = fetch("../src/api/archiveChats.php?cid_arc="+chatId+"&status=1")
                .then(res => res.json())
                .then(data => { 
                    if(data.status == "error" ){
                        const modal = new bootstrap.Modal(document.getElementById('archiveSuccessModal'));
                        modal.show();
                    } else {
                        const modal = new bootstrap.Modal(document.getElementById('archiveSuccessModal'));
                        modal.show();
                    }
                });
        
}

function unArchiveChat(chatId){
        const resp = fetch("../src/api/archiveChats.php?cid_arc="+chatId+"&status=0")
                .then(res => res.json())
                .then(data =>console.log(data))
                .then(()=> {
                        window.location.reload();
                });
        
}

function loadChats() {
    console.log()
    fetch("../src/api/getChats.php")
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("userList-container");
            const archive = document.getElementById("archive-container");
           
            data.forEach(chat => {
                 let onClick = (chat.archived == 1)?`unArchiveChat(${chat.id})`:`archiveChat(${chat.id})`;
                 let buttonText =  (chat.archived == 1)?"remove from archive":"Archive";


                const card = document.createElement("div");
                card.className = "card mb-2 shadow-sm";

                card.innerHTML = `
                    <div class="card-body d-flex align-items-center">
                        <img src="${chat.picture}" 
                             class="rounded-circle me-3" 
                             width="50" height="50" 
                             style="object-fit: cover;">

                        <div class="flex-grow-1">
                            <a href="./chat.php?cid=${chat.id}" 
                               class="fw-bold text-decoration-none text-dark">
                               ${chat.name}
                            </a>
                            <p class="mb-0 text-muted small">
                                ${chat.description}
                            </p>
                        </div>

                        <button class="btn btn-outline-secondary btn-sm ms-auto"
                                onclick="${onClick}">
                            ${buttonText}
                        </button>
                    </div>
                `;
                if (chat.archived == 1) {
                        archive.appendChild(card);
                } else {
                        container.appendChild(card);
                }   
                
            });
        })
        .catch(err => console.error(err));
}

document.addEventListener("DOMContentLoaded", () => {
    
    loadChats();
});


document.getElementById('search-btn').addEventListener("click", searchChat);