<?php
require_once __DIR__ . '/../src/bootstrap.php';

// Resolve session
$sid = $_COOKIE['SID'] ?? '';
$user = null;
if ($sid) {
    $j = $redis->get("session_$sid");
    if ($j) $user = json_decode($j, true);
}
if (!$user) {
    header("Location: login.php");
    exit;
}

$owner = $user['user'];
$message = "";

// Create note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $content = trim($_POST['content'] ?? '');
    if ($content !== '') {
        $s = $pdo->prepare("INSERT INTO notes (owner, content) VALUES (?, ?)");
        $s->execute([$owner, $content]);
        $message = "Note created.";
    }
}

// Update note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id = intval($_POST['id']);
    if ($id <= 0) { $message = "Invalid note ID"; }

    $content = trim($_POST['content'] ?? '');
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

    $ch = $pdo->prepare("SELECT owner FROM notes WHERE id=?");
    $ch->execute([$id]);
    $row = $ch->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['owner'] === $owner) {
        $u = $pdo->prepare("UPDATE notes SET content=? WHERE id=?");
        $u->execute([$content, $id]);
        $message = "Note updated.";
    } else {
        $message = "Unauthorized.";
    }
}

// Delete note
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = intval($_POST['id']);
    if ($id <= 0) { $message = "Invalid note ID"; }

    $d = $pdo->prepare("DELETE FROM notes WHERE id=? AND owner=?");
    $d->execute([$id, $owner]);
    $message = "Note deleted.";
}

// Fetch notes
$q = $pdo->prepare("SELECT id, content, created_at FROM notes WHERE owner=? ORDER BY id DESC");
$q->execute([$owner]);
$notes = $q->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../templates/header.php';
?>

<div class="container py-4">

<h3 class="mb-3">Your Notes</h3>

<?php if ($message): ?>
  <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>


<!-- NORMAL NEW NOTE -->
<div class="card mb-4 shadow-sm">
  <div class="card-header fw-bold">Create New Note</div>
  <div class="card-body">
    <form method="post">
      <input type="hidden" name="action" value="create">
      <textarea name="content" class="form-control" rows="3" placeholder="Write a new note..."></textarea>
      <button class="btn btn-primary mt-2">Add Note</button>
    </form>
  </div>
</div>


<!-- IMPORT FROM URL -->
<div class="card mb-4 shadow-sm">
  <div class="card-header fw-bold">Import Note From URL</div>
  <div class="card-body">
    <input id="import_url" type="text" class="form-control" placeholder="https://example.com/article">
    <button id="importBtn" class="btn btn-outline-primary mt-2">Import From URL</button>
  </div>
</div>


<!-- LIST NOTES -->
<?php if ($notes): ?>
  <?php foreach ($notes as $n): ?>
    <div class="card mb-3 shadow-sm">
      <div class="card-body">

        <small class="text-muted"><?= htmlspecialchars($n['created_at']) ?></small>

        <p class="mt-2"><?= nl2br(htmlspecialchars($n['content'])) ?></p>

        <form method="post" class="mt-2">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= $n['id'] ?>">
          <textarea name="content" class="form-control" rows="2"><?= htmlspecialchars($n['content']) ?></textarea>
          <button class="btn btn-success btn-sm mt-1">Save</button>
        </form>

        <form method="post" class="mt-2">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $n['id'] ?>">
          <button class="btn btn-danger btn-sm">Delete</button>
        </form>

      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p class="text-muted">No notes yet.</p>
<?php endif; ?>

</div>

<script>
document.getElementById("importBtn").addEventListener("click", async () => {
    const url = document.getElementById("import_url").value.trim();
    if (!url) {
        alert("Enter a URL first.");
        return;
    }

    let form = new FormData();
    form.append("url", url);

    let res = await fetch("import_note.php", {
        method: "POST",
        body: form
    });

    let data = await res.json();
    if (data.status === "ok") {
        alert("Imported!");
        location.reload();
    } else {
        alert("Error: " + data.msg);
    }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>


