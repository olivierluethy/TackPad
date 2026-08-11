<?php
/**
 * Shared off-canvas sidebar (task list + calendar both include this).
 *
 * Expects:
 *   $username    string   greeting name.
 *   $avatarSrc   string    resolved avatar src ('' → monogram fallback).
 *   $initial     string    monogram letter.
 *   $sidebarActive string  'tasks' | 'calendar' — which primary link to swap.
 */
$sidebarActive = $sidebarActive ?? 'tasks';
$avatarSrc = $avatarSrc ?? '';
$initial = $initial ?? '?';
?>
<?php
/* Restore the drawer's saved open/closed state BEFORE the browser paints, so it
   never flickers open or closed on navigation. This runs during parse — and the
   sidebar is the first element in <body> on every page — so the <html> class is
   set before any content is painted. Kept in sync with tackpad.js (SIDENAV_KEY).
   Default (no stored value): open on wide screens, closed on narrow/mobile. */
?>
<script>
  (function () {
    try {
      var stored = localStorage.getItem("tackpad.sidenav");
      var open = stored === null
        ? window.matchMedia("(min-width: 768px)").matches
        : stored === "1";
      var root = document.documentElement;
      root.classList.add("sidenav-preload"); // no animation on the initial state
      if (open) root.classList.add("sidenav-open");
    } catch (e) {
      /* localStorage/matchMedia unavailable — fall back to CSS default (closed) */
    }
  })();
</script>

<nav id="mySidenav" class="sidenav" aria-label="Main">
    <div class="sidenav-inner">
        <button type="button" class="sidenav-close" onclick="closeNav()" aria-label="Close menu">
            <span aria-hidden="true">&times;</span>
        </button>

        <div class="sidenav-header">
            <!-- Current user's avatar: uploaded file or confirmed URL, else monogram. -->
            <button type="button" class="avatar avatar-lg sidenav-avatar"
                title="Edit your profile" onclick="openProfileModal()">
                <?php if ($avatarSrc !== ''): ?>
                    <img src="<?= e($avatarSrc) ?>" alt="Your avatar">
                <?php else: ?>
                    <span><?= e($initial) ?></span>
                <?php endif; ?>
            </button>

            <div class="brand-mark">TackPad</div>
            <div class="brand-greeting">Hello <?= e($username) ?>!</div>
        </div>

        <div class="sidenav-links">
            <a onclick="openProfileModal()"><i class="fas fa-user-circle"></i><span>Profile</span></a>
            <?php if ($sidebarActive === 'calendar'): ?>
                <a href="home"><i class="fas fa-list-ul"></i><span>Tasks</span></a>
            <?php else: ?>
                <a href="calendar"><i class="fas fa-calendar-alt"></i><span>Calendar</span></a>
            <?php endif; ?>
        </div>

        <a href="logout" class="sidenav-logout"><i class="fas fa-sign-out-alt"></i><span>Log out</span></a>
    </div>
</nav>
<button type="button" class="navi" onclick="openNav()" aria-label="Open menu">&#9776;</button>
