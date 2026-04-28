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

<div class="conversation-page d-flex">

    <aside class="conversation-sidebar p-3">

        <div class="sidebar-search mb-3">
            <div class="d-flex gap-2" role="search">
                <input 
                    class="form-control"
                    type="search"
                    placeholder="Search"
                    id="search-input"
                    aria-label="Search"
                />
                <button class="btn btn-outline-success" id="search-btn">
                    Search
                </button>
            </div>
        </div>

        <div id="userList-container" class="chat-list mb-3">
            <button 
                onclick='showContainer("archive-container")' 
                class="btn btn-outline-secondary btn-sm">
                Show Archive
            </button>
        </div>

        <div id="archive-container" class="chat-list d-none">
            <button 
                onclick='showContainer("userList-container")' 
                class="btn btn-outline-secondary btn-sm">
                Show Chats
            </button>
        </div>

    </aside>

    <main class="conversation-main flex-grow-1 d-flex justify-content-center align-items-center">

        <div class="empty-state text-center text-muted">
            <h3>Select a conversation</h3>
            <p class="mb-0">Your messages will appear here.</p>
        </div>

    </main>

</div>

<?php include '../src/includes/footer.php'; ?>

</body>
</html>