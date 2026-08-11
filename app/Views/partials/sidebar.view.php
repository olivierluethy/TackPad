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
<div id="mySidenav" class="sidenav">
    <a class="closebtn" style="position:absolute;top:10px;right:14px;font-size:24px" onclick="closeNav()">&times;</a>

    <!-- Current user's avatar: uploaded file or confirmed URL, else monogram. -->
    <button type="button" class="avatar avatar-lg" style="border:none;cursor:pointer;margin-bottom:16px"
        title="Edit your profile" onclick="openProfileModal()">
        <?php if ($avatarSrc !== ''): ?>
            <img src="<?= e($avatarSrc) ?>" alt="Your avatar">
        <?php else: ?>
            <span><?= e($initial) ?></span>
        <?php endif; ?>
    </button>

    <div class="brand-mark">TackPad</div>
    <div class="brand-greeting">Hello <?= e($username) ?>!</div>

    <a onclick="openProfileModal()"><i class="fas fa-user-circle"></i>&nbsp;Profile</a>
    <?php if ($sidebarActive === 'calendar'): ?>
        <a href="home"><i class="fas fa-list-ul"></i>&nbsp;Tasks</a>
    <?php else: ?>
        <a href="calendar"><i class="fas fa-calendar-alt"></i>&nbsp;Calendar</a>
    <?php endif; ?>
    <a href="logout"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</a>
</div>
<span class='navi' onclick="openNav()">&#9776;</span>
