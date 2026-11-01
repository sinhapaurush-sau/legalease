
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = trim($_GET['search'] ?? '');
$court = trim($_GET['court'] ?? '');
$year = trim($_GET['year'] ?? '');
$act = trim($_GET['act'] ?? '');

// Fetch distinct courts and acts for filters
$courts = $conn->query("
    SELECT DISTINCT court
    FROM legal_documents
    WHERE court IS NOT NULL AND court != ''
    ORDER BY court
");

$acts = $conn->query("
    SELECT DISTINCT act_name
    FROM legal_documents
    WHERE act_name IS NOT NULL AND act_name != ''
    ORDER BY act_name
");

// Build search query
$sql = "
    SELECT *
    FROM legal_documents
    WHERE 1=1
";

$params = [];
$types = "";

if ($search != '') {
    $sql .= " AND (
        title LIKE ?
        OR act_name LIKE ?
        OR section LIKE ?
        OR content LIKE ?
        OR document_type LIKE ?
        OR judge LIKE ?
    )";

    $keyword = "%$search%";

    for ($i = 0; $i < 6; $i++) {
        $params[] = $keyword;
        $types .= "s";
    }
}

if ($court != '') {
    $sql .= " AND court = ?";
    $params[] = $court;
    $types .= "s";
}

if ($act != '') {
    $sql .= " AND act_name = ?";
    $params[] = $act;
    $types .= "s";
}

if ($year != '' && ctype_digit($year)) {
    $sql .= " AND year = ?";
    $params[] = (int)$year;
    $types .= "i";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$documents = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Legal Research - LegalEase</title>
    <link rel="stylesheet" href="css/research.css">
</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">

        <div class="brand">
            <h2>LegalEase</h2>
            <p>Legal Management System</p>
        </div>

        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="clients.php">Clients</a>
            <a href="cases.php">Cases</a>
            <a href="research.php" class="active">Legal Research</a>
            <a href="bookmarks.php">Documents</a>
            <a href="hearings.php">Hearings</a>
            <a href="tasks.php">Tasks & Notes</a>
        </nav>

        <div class="sidebar-bottom">
            <a href="profile.php">My Profile</a>
            <a href="logout.php">Logout</a>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="main-content">

        <header class="topbar">
            <div>
                <h2>Legal Research</h2>
                <p>Search legal documents and judgments</p>
            </div>

            <div class="user-info">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </div>
        </header>

        <div class="content">

            <div class="page-header">
                <div>
                    <h3>Research Portal</h3>
                    <p>Find Acts, sections and legal judgments.</p>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="search-card">

                <form method="GET">

                    <div class="search-input">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search by title, keyword, Act or section..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                        <button type="submit">Search</button>
                    </div>

                    <div class="filters">

                        <div>
                            <label>Court</label>

                            <select name="court">
                                <option value="">All Courts</option>

                                <?php while ($row = $courts->fetch_assoc()) { ?>
                                    <option
                                        value="<?php echo htmlspecialchars($row['court']); ?>"
                                        <?php echo $court == $row['court'] ? 'selected' : ''; ?>
                                    >
                                        <?php echo htmlspecialchars($row['court']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div>
                            <label>Act</label>

                            <select name="act">
                                <option value="">All Acts</option>

                                <?php while ($row = $acts->fetch_assoc()) { ?>
                                    <option
                                        value="<?php echo htmlspecialchars($row['act_name']); ?>"
                                        <?php echo $act == $row['act_name'] ? 'selected' : ''; ?>
                                    >
                                        <?php echo htmlspecialchars($row['act_name']); ?>
                                    </option>
                                <?php } ?>

                            </select>
                        </div>

                        <div>
                            <label>Year</label>

                            <input
                                type="number"
                                name="year"
                                placeholder="Enter year"
                                value="<?php echo htmlspecialchars($year); ?>"
                            >
                        </div>

                        <button type="submit" class="filter-btn">
                            Apply Filters
                        </button>

                        <a href="research.php" class="reset-btn">
                            Clear
                        </a>

                    </div>

                </form>

            </div>

            <!-- Search Results -->
            <div class="results-section">

                <div class="results-header">
                    <h3>Search Results</h3>
                    <span><?php echo $documents->num_rows; ?> documents found</span>
                </div>

                <?php if ($documents->num_rows > 0) { ?>

                    <?php while ($doc = $documents->fetch_assoc()) { ?>

                        <div class="document-card">

                            <div class="document-info">

                                <span class="document-type">
                                    <?php echo htmlspecialchars($doc['document_type']); ?>
                                </span>

                                <h3>
                                    <?php echo htmlspecialchars($doc['title']); ?>
                                </h3>

                                <p class="document-meta">
                                    <?php echo htmlspecialchars($doc['court']); ?>

                                    <?php if ($doc['year']) { ?>
                                        | <?php echo htmlspecialchars($doc['year']); ?>
                                    <?php } ?>

                                    <?php if ($doc['judge']) { ?>
                                        | Judge: <?php echo htmlspecialchars($doc['judge']); ?>
                                    <?php } ?>
                                </p>

                                <p class="document-act">
                                    Act: <?php echo htmlspecialchars($doc['act_name']); ?>

                                    <?php if ($doc['section']) { ?>
                                        | Section: <?php echo htmlspecialchars($doc['section']); ?>
                                    <?php } ?>
                                </p>

                                <p class="document-content">
                                    <?php
                                    $content = $doc['content'] ?? '';

                                    echo htmlspecialchars(
                                        strlen($content) > 200
                                            ? substr($content, 0, 200) . '...'
                                            : $content
                                    );
                                    ?>
                                </p>

                                <a
                                    href="legal-document.php?id=<?php echo $doc['legal_document_id']; ?>"
                                    class="view-btn"
                                >
                                    Read Document
                                </a>

                            </div>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="empty">
                        No legal documents found.
                    </div>

                <?php } ?>

            </div>

        </div>

    </main>

</div>

</body>
</html>
