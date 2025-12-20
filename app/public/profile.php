<?php 
require_once __DIR__ . '/../src/bootstrap.php';

// Resolve session
$sid = $_COOKIE['SID'] ?? '';
$user = null;
if ($sid) {
    $user_json = $redis->get("session_$sid");
    if ($user_json) {
        $user = json_decode($user_json, true);
    }
}
if (!$user) {
    header('Location: login.php');
    exit;
}

// Fetch profile from DB
$stmt = $GLOBALS['pdo']->prepare(
    "SELECT username, role, display_name, bio, avatar_path FROM users WHERE username = ?"
);
$stmt->execute([$user['user']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Safe defaults
$profile = $profile ?: [
    'username' => $user['user'] ?? '',
    'role' => $user['role'] ?? 'user',
    'display_name' => '',
    'bio' => '',
    'avatar_path' => ''
];

require_once __DIR__ . '/../templates/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
  .profile-wrapper {
      max-width: 1000px;
      margin: auto;
  }

  .profile-card {
      background: white;
      border-radius: 22px;
      padding: 40px 30px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.06);
      text-align: center;
      transition: 0.25s ease;
  }

  .profile-card:hover {
      transform: translateY(-3px);
  }

  .avatar-large {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #eaeaea;
      margin-bottom: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  }

  .avatar-placeholder {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      background: #f5f5f5;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #777;
      font-size: 0.95rem;
      border: 4px solid #eaeaea;
      margin-bottom: 15px;
  }

  .profile-name {
      font-size: 1.75rem;
      font-weight: 700;
      color: #333;
  }

  .profile-role {
      margin-top: 8px;
      display: inline-block;
      padding: 6px 14px;
      border-radius: 20px;
      background: #111;
      color: white;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
  }

  .profile-bio {
      margin-top: 18px;
      font-size: 0.95rem;
      color: #666;
      line-height: 1.5;
  }

  .section-card {
      border-radius: 20px;
      padding: 28px;
      background: white;
      box-shadow: 0 12px 30px rgba(0,0,0,0.05);
  }

  .section-title {
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 18px;
      color: #333;
  }

</style>


<main class="profile-wrapper py-5">

  <div class="row g-4">

    <!-- LEFT: Beautiful Profile Card -->
    <div class="col-lg-4">
      <div class="profile-card">

        <!-- Avatar -->
        <?php if ($profile['avatar_path']): ?>
          <img src="<?= htmlspecialchars($profile['avatar_path']) ?>"
               class="avatar-large">
        <?php else: ?>
          <div class="avatar-placeholder">No Avatar</div>
        <?php endif; ?>

        <!-- Name -->
        <div class="profile-name">
          <?= htmlspecialchars($profile['display_name'] ?: $profile['username']) ?>
        </div>

        <!-- Role badge -->
        <span class="profile-role"><?= htmlspecialchars($profile['role']) ?></span>

        <!-- Bio -->
        <p class="profile-bio">
          <?= nl2br(htmlspecialchars($profile['bio'] ?: "No bio added yet.")) ?>
        </p>

      </div>


      <!-- Upload Avatar -->
      <div class="section-card mt-4">
        <div class="section-title">
          <i class="bi bi-image"></i> Update Avatar
        </div>

        <form method="post" action="upload.php" enctype="multipart/form-data">
          <input type="file" name="avatar" class="form-control mb-3" accept=".png,.jpg,.jpeg">
          <button class="btn btn-primary w-100 py-2">Upload</button>
        </form>
      </div>

    </div>


    <!-- RIGHT: Profile Editing -->
    <div class="col-lg-8">
      <div class="section-card">

        <div class="section-title">
          <i class="bi bi-pencil-square"></i> Edit Profile
        </div>

        <form method="post" action="update_profile.php">
          <input type="hidden" name="username" value="<?= htmlspecialchars($profile['username']) ?>">

          <div class="mb-3">
            <label class="form-label fw-semibold">Display Name</label>
            <input name="display_name"
                   class="form-control form-control-lg"
                   value="<?= htmlspecialchars($profile['display_name']) ?>">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Bio</label>
            <textarea name="bio" class="form-control form-control-lg" rows="4"><?= htmlspecialchars($profile['bio']) ?></textarea>
          </div>

          <button class="btn btn-success px-4 py-2">Save Changes</button>
        </form>

      </div>
    </div>

  </div>

</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>