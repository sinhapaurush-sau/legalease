
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


$acts = $conn->query("
    SELECT DISTINCT act_name
    FROM legal_documents
    WHERE act_name IS NOT NULL AND act_name != ''
    ORDER BY act_name
");

$sql = "
    SELECT *
    FROM (
        SELECT
            cid AS id,
            applicant AS title,
            defendent AS parties,
            body AS content,
            'Landmark Judgment' AS document_type,
            'Supreme Court of India' AS court,
            NULL AS judge,
            CAST(
                SUBSTRING_INDEX(body, ' | ', 1)
                AS UNSIGNED
            ) AS year,
            NULL AS act_name,
            NULL AS section,
            created_at,
            'case' AS source

        FROM cases
        WHERE status = 'Landmark'

        UNION ALL

        SELECT
            legal_document_id AS id,
            title,
            NULL AS parties,
            content,
            document_type,
            court,
            judge,
            year,
            act_name,
            section,
            created_at,
            'document' AS source

        FROM legal_documents
    ) AS research
    WHERE 1=1
";

$params = [];
$types = "";
if ($search !== '') {

    $sql .= "
        AND (
            title LIKE ?
            OR parties LIKE ?
            OR content LIKE ?
            OR document_type LIKE ?
            OR judge LIKE ?
            OR act_name LIKE ?
            OR section LIKE ?
        )
    ";

    $keyword = "%$search%";

    for ($i = 0; $i < 7; $i++) {
        $params[] = $keyword;
        $types .= "s";
    }
}

if ($court !== '') {
    $sql .= " AND court = ?";

    $params[] = $court;
    $types .= "s";
}

if ($act !== '') {
    $sql .= " AND act_name = ?";

    $params[] = $act;
    $types .= "s";
}


if ($year !== '' && ctype_digit($year)) {
    $sql .= " AND year = ?";

    $params[] = (int)$year;
    $types .= "i";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query preparation failed.");
}

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
    <aside class="sidebar">

        <div class="brand">
            <h2>LegalEase</h2>
            <p>Legal Management System</p>
        </div>

        <nav>

            <a href="dashboard.php">Dashboard</a>

            <a href="clients.php">Clients</a>

            <a href="cases.php">Cases</a>

            <a href="research.php" class="active">
                Legal Research
            </a>

            <a href="bookmarks.php">Documents</a>

            <a href="hearings.php">Hearings</a>

            <a href="tasks.php">Tasks & Notes</a>

        </nav>

        <div class="sidebar-bottom">

            <a href="profile.php">My Profile</a>

            <a href="logout.php">Logout</a>

        </div>

    </aside>
    <main class="main-content">
        <header class="topbar">

            <div>

                <h2>Legal Research</h2>

                <p>Search legal documents and judgments</p>

            </div>

            <div class="user-info">

                <?php
                echo htmlspecialchars($_SESSION['user_name']);
                ?>

            </div>

        </header>
        <div class="content">
            <div class="page-header">

                <div>

                    <h3>Research Portal</h3>

                    <p>
                        Find Acts, sections, legal judgments
                        and landmark court decisions.
                    </p>

                </div>

            </div>
            <div class="search-card">

                <form method="GET">

                    <div class="search-input">

                        <input
                            type="text"
                            name="search"
                            placeholder="Search by title, keyword, Act or section..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                        <button type="submit">
                            Search
                        </button>

                    </div>


                    <div class="filters">
                        <div>

                            <label>Act</label>

                            <select name="act">

                                <option value="">
                                    All Acts
                                </option>

                                <?php while ($row = $acts->fetch_assoc()) { ?>

                                    <option
                                        value="<?php echo htmlspecialchars($row['act_name']); ?>"
                                        <?php echo $act === $row['act_name'] ? 'selected' : ''; ?>
                                    >

                                        <?php
                                        echo htmlspecialchars($row['act_name']);
                                        ?>

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
                                min="1800"
                                max="2100"
                                value="<?php echo htmlspecialchars($year); ?>"
                            >

                        </div>


                        <button
                            type="submit"
                            class="filter-btn"
                        >
                            Apply Filters
                        </button>


                        <a
                            href="research.php"
                            class="reset-btn"
                        >
                            Clear
                        </a>

                    </div>

                </form>

            </div>


            <!-- Search Results -->

            <div class="results-section">

                <div class="results-header">

                    <h3>Search Results</h3>

                    <span>
                        <?php echo $documents->num_rows; ?>
                        documents found
                    </span>

                </div>


                <?php if ($documents->num_rows > 0) { ?>


                    <?php while ($doc = $documents->fetch_assoc()) { ?>


                        <div class="document-card">

                            <div class="document-info">


                                <!-- Document Type -->

                                <span class="document-type">

                                    <?php
                                    echo htmlspecialchars(
                                        $doc['document_type'] ?? 'Document'
                                    );
                                    ?>

                                </span>


                                <!-- Title -->

                                <h3>

                                    <?php
                                    echo htmlspecialchars(
                                        $doc['title'] ?? 'Untitled'
                                    );
                                    ?>

                                </h3>


                                <!-- Parties -->

                                <?php if (!empty($doc['parties'])) { ?>

                                    <p class="document-meta">

                                        <strong>Versus:</strong>

                                        <?php
                                        echo htmlspecialchars($doc['parties']);
                                        ?>

                                    </p>

                                <?php } ?>


                                <!-- Court, Year and Judge -->

                                <p class="document-meta">

                                    <?php
                                    echo htmlspecialchars($doc['court'] ?? '');
                                    ?>

                                    <?php if (!empty($doc['year'])) { ?>

                                        | <?php
                                        echo htmlspecialchars($doc['year']);
                                        ?>

                                    <?php } ?>


                                    <?php if (!empty($doc['judge'])) { ?>

                                        | Judge:

                                        <?php
                                        echo htmlspecialchars($doc['judge']);
                                        ?>

                                    <?php } ?>

                                </p>


                                <!-- Act and Section -->

                                <?php if (!empty($doc['act_name'])) { ?>

                                    <p class="document-act">

                                        Act:

                                        <?php
                                        echo htmlspecialchars($doc['act_name']);
                                        ?>


                                        <?php if (!empty($doc['section'])) { ?>

                                            | Section:

                                            <?php
                                            echo htmlspecialchars($doc['section']);
                                            ?>

                                        <?php } ?>

                                    </p>

                                <?php } ?>


                                <!-- Content -->

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


                                <!-- View Button -->

                                <?php if ($doc['source'] === 'case') { ?>

                                    <a
                                        href="case-details.php?id=<?php echo (int)$doc['id']; ?>"
                                        class="view-btn"
                                    >
                                        View Judgment
                                    </a>

                                <?php } else { ?>

                                    <a
                                        href="legal-document.php?id=<?php echo (int)$doc['id']; ?>"
                                        class="view-btn"
                                    >
                                        Read Document
                                    </a>

                                <?php } ?>


                            </div>

                        </div>


                    <?php } ?>


                <?php } else { ?>


                    <div class="empty">

                        No legal documents or judgments found.

                    </div>


                <?php } ?>


            </div>

        </div>

    </main>

</div>

</body>

</html>