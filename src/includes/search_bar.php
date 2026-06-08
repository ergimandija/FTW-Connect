<?php
// Assuming $con (PDO) is initialized via header.php or parent scripts
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$searchResults = [];

if ($query !== '') {
    try {
        $searchStmt = $con->prepare("
            SELECT id, name, email, profilePicturePath 
            FROM users 
            WHERE name LIKE ? OR email LIKE ? 
            LIMIT 10
        ");
        $likeParam = "%" . $query . "%";
        $searchStmt->execute([$likeParam, $likeParam]);
        $searchResults = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Search Error: " . $e->getMessage());
    }
}
?>

<style>
    .search-card-lean {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
    }
    .search-wrapper {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .search-input-group-lean {
        border-radius: 6px;
        overflow: hidden;
        flex-grow: 1;
    }
    .search-addon-lean {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-right: none;
        color: #6c757d;
        padding-left: 12px;
        padding-right: 8px;
    }
    .search-field-lean {
        border: 1px solid #dee2e6;
        border-left: none;
        padding-top: 8px;
        padding-bottom: 8px;
        font-size: 0.95rem;
    }
    .search-field-lean:focus {
        border-color: var(--ftw-blue);
        box-shadow: none;
    }
    .search-input-group-lean:focus-within .search-addon-lean,
    .search-input-group-lean:focus-within .search-field-lean {
        border-color: var(--ftw-blue);
    }
    .btn-search-lean {
        padding-top: 8px;
        padding-bottom: 8px;
        font-size: 0.9rem;
        white-space: nowrap;
    }
    
    /* Results List Styling */
    .search-results-list {
        border-top: 1px solid #eee;
    }
    .search-user-item {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        text-decoration: none;
        color: #333;
        border-radius: 6px;
        transition: background-color 0.2s ease;
    }
    .search-user-item:hover {
        background-color: rgba(17, 99, 149, 0.05); 
        color: var(--ftw-blue);
    }
    .search-avatar {
        width: 36px;
        height: 36px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 12px;
        border: 1px solid #dee2e6;
    }
    .search-user-info {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }
    .search-user-name {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .search-user-email {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>

<div class="row mb-3">
    <div class="col-12">
        <div class="card search-card-lean p-2">
            <form action="" method="GET" class="m-0">
                <div class="search-wrapper">
                    <div class="input-group search-input-group-lean">
                        <span class="input-group-text search-addon-lean">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="query" class="form-control search-field-lean" 
                               placeholder="Search users by name or email..." 
                               value="<?= htmlspecialchars($query) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-ftw btn-search-lean">Search</button>
                    
                    <?php if ($query !== ''): ?>
                        <a href="?" class="btn btn-sm btn-light py-2 text-muted" title="Clear Search">✕</a>
                    <?php endif; ?>
                </div>
            </form>

            <?php if ($query !== ''): ?>
                <div class="search-results-list mt-2 pt-2">
                    <?php if (empty($searchResults)): ?>
                        <div class="text-center py-3 text-muted small">
                            <i class="bi bi-person-x me-1"></i> No users found matching "<strong><?= htmlspecialchars($query) ?></strong>"
                        </div>
                    <?php else: ?>
                        <div class="text-muted small px-2 mb-2 fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            Search Results (<?= count($searchResults) ?>)
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <?php foreach ($searchResults as $row): ?>
                                <a href="profile.php?id=<?= $row['id'] ?>" class="search-user-item">
                                    <img src="<?= htmlspecialchars($row['profilePicturePath'] ?? 'assets/img/anonymous.png') ?>" 
                                         class="search-avatar" alt="Avatar">
                                    <div class="search-user-info">
                                        <span class="search-user-name"><?= htmlspecialchars($row['name']) ?></span>
                                        <span class="search-user-email"><?= htmlspecialchars($row['email']) ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>