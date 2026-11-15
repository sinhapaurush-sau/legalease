
<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$practitioner_id = (int) $_SESSION['user_id'];
$search = trim($_GET['search'] ?? '');

// Fetch only clients belonging to the logged-in practitioner
$sql = "
    SELECT *
    FROM client
    WHERE practitioner_id = ?
";

$params = [$practitioner_id];
$types = "i";

// Search within practitioner's own clients
if ($search !== "") {
    $sql .= "
        AND (
            name LIKE ?
            OR phone LIKE ?
        )
    ";

    $keyword = "%{$search}%";

    $params[] = $keyword;
    $params[] = $keyword;

    $types .= "ss";
}

$sql .= " ORDER BY client_id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Unable to fetch clients.");
}

$stmt->bind_param($types, ...$params);
$stmt->execute();

$clients = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Clients - LegalEase</title>

    <link rel="stylesheet" href="css/clients.css">
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
            <a href="clients.php" class="active">Clients</a>
            <a href="cases.php">Cases</a>
            <a href="research.php">Legal Research</a>
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
                <h2>Clients</h2>
                <p>Manage your client information</p>
            </div>

            <div class="user-info">
                <?php
                echo htmlspecialchars($_SESSION['user_name']);
                ?>
            </div>

        </header>


        <div class="content">

            <!-- Page Header -->
            <div class="page-header">

                <div>
                    <h3>All Clients</h3>
                    <p>View and manage your registered clients.</p>
                </div>

                <a href="client-form.php" class="add-btn">
                    + Add Client
                </a>

            </div>


            <!-- Search -->
            <div class="search-box">

                <form method="GET">

                    <input
                        type="text"
                        name="search"
                        placeholder="Search by name or phone..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                    <button type="submit">
                        Search
                    </button>

                    <?php if ($search !== "") { ?>

                        <a href="clients.php" class="reset-btn">
                            Clear
                        </a>

                    <?php } ?>

                </form>

            </div>


            <!-- Client Table -->
            <div class="table-container">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client Name</th>
                            <th>Phone</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($clients->num_rows > 0) { ?>

                            <?php while ($client = $clients->fetch_assoc()) { ?>

                                <tr>

                                    <td>
                                        #<?php echo (int) $client['client_id']; ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars($client['name'] ?? '');
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        echo htmlspecialchars($client['phone'] ?? '');
                                        ?>
                                    </td>

                                    <td>
                                        <?php

                                        $note = $client['note'] ?? '';

                                        echo htmlspecialchars(
                                            strlen($note) > 50
                                                ? substr($note, 0, 50) . '...'
                                                : $note
                                        );

                                        ?>
                                    </td>

                                    <td class="actions">

                                        <a
                                            href="client-form.php?id=<?php echo (int) $client['client_id']; ?>"
                                            class="edit-btn"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="client-delete.php?id=<?php echo (int) $client['client_id']; ?>"
                                            class="delete-btn"
                                            onclick="return confirm('Are you sure you want to delete this client?')"
                                        >
                                            Delete
                                        </a>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>
                                <td colspan="5" class="empty">
                                    <?php echo $search !== ''
                                        ? 'No matching clients found.'
                                        : 'No clients registered yet.'; ?>
                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>
