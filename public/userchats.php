
<!DOCTYPE html>
<html lang="en">
<?php 
include '../src/includes/header.php';


?>

<div class="modal fade" id="archiveSuccessModal" tabindex="-1" aria-labelledby="successModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Success</h5>
                <button type="button" class="btn-close btn-close-white" onclick="window.location.reload()"></button>            
            </div>
            <div class="modal-body">
                <p class="mb-0">Chat was archived</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="window.location.reload()">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/userchats.js" defer></script>
<div class="p-3 m-3">
        <div class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" id="search-input" aria-label="Search"/>
                <button class="btn btn-outline-success" id="search-btn">Search</button>
        </div>
</div>
<div id="userList-container" class="container mt-3">
    <button onclick='showContainer("archive-container")' class="btn"> Show Archive</button>
</div>
<div id="archive-container" class="container mt-3  d-none">
    <button onclick='showContainer("userList-container")' class="btn"> Show Chats</button>
</div>  
    

</body>
</html>
