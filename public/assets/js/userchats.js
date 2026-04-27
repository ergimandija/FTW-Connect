function displayUser(){
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

document.getElementById('search-btn').addEventListener("click", displayUser);