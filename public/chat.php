<!DOCTYPE html>
<html lang="en">
<?php include '../src/includes/header.php'; ?> 

<script src="assets/js/chat.js" defer></script>

<body class="bg-light">

<div class="container py-3">

    <!-- Header -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <h5 class="mb-0">💬 Chat</h5>
        </div>
    </div>

    <!-- Chat box -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div id="messageContainer" class="chat-box p-3 overflow-auto"></div>

            <!-- Input -->
            <form id="chatForm" class="border-top p-2 d-flex gap-2 bg-white">
                <input 
                    type="text" 
                    id="message" 
                    class="form-control" 
                    placeholder="Type a message..."
                    autocomplete="off"
                >

                <button class="btn btn-primary px-4" type="submit">
                    Send
                </button>

                <input type="hidden" id="chatId" value="<?= $_GET['cid']; ?>">
                <input type="hidden" id="uid_reference" value="<?= $_SESSION['uid'] ?>">
                <input type="hidden" id="loadCount" value="1">
            </form>

        </div>
    </div>

</div>

</body>
</html>