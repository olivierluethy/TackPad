<?php
/**
 * Unified authentication screen. One page that toggles between Login and
 * Register (client-side, via Alpine) — no separate routes to click through.
 *
 * Expected variables (all optional, defaulted here so the view is safe to
 * render on a bare GET):
 *   $mode    'login' | 'register' — which panel is active on load.
 *   $errors  array<string,string> — field => message from a failed POST.
 *   $old     array<string,string> — previously entered values to repopulate.
 */
$mode = $mode ?? 'login';
$errors = $errors ?? [];
$old = $old ?? [];
$err = fn(string $field): string => isset($errors[$field]) ? e($errors[$field]) : '';
$oldEmail = isset($old['email']) ? e($old['email']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TackPad &middot; Sign in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Olivier Luethy">
    <link rel="shortcut icon" href="assets/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="public/css/tackpad.css">
    <script defer src="public/js/auth.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</head>

<body class="auth-body on-dark" x-data="authScreen('<?= e($mode) ?>')">
    <div class="auth-shell">
        <h1 class="auth-title">TackPad</h1>

        <div class="auth-card">
            <div class="auth-toggle" role="tablist" aria-label="Authentication mode">
                <button type="button" role="tab" :aria-selected="mode === 'login'"
                    :class="{ 'active': mode === 'login' }" @click="mode = 'login'">Log in</button>
                <button type="button" role="tab" :aria-selected="mode === 'register'"
                    :class="{ 'active': mode === 'register' }" @click="mode = 'register'">Sign up</button>
            </div>

            <!-- ============ LOGIN ============ -->
            <form method="post" action="login" x-show="mode === 'login'" x-transition.opacity>
                <div class="auth-field">
                    <label for="login_email">Email</label>
                    <input class="auth-input <?= isset($errors['email']) && $mode === 'login' ? 'is-invalid' : '' ?>"
                        type="email" id="login_email" name="email" autocomplete="email"
                        value="<?= $mode === 'login' ? $oldEmail : '' ?>">
                    <p class="auth-error"><?= $mode === 'login' ? $err('email') : '' ?></p>
                </div>
                <div class="auth-field">
                    <label for="login_password">Password</label>
                    <input class="auth-input <?= isset($errors['password']) && $mode === 'login' ? 'is-invalid' : '' ?>"
                        type="password" id="login_password" name="password" autocomplete="current-password">
                    <p class="auth-error"><?= $mode === 'login' ? $err('password') : '' ?></p>
                </div>
                <button class="auth-submit" type="submit">Log in</button>
                <p class="auth-switch">New to TackPad?
                    <button type="button" @click="mode = 'register'">Create an account</button>
                </p>
            </form>

            <!-- ============ REGISTER ============ -->
            <form method="post" action="register" x-show="mode === 'register'" x-transition.opacity x-cloak>
                <div class="auth-field">
                    <label for="reg_email">Email</label>
                    <input class="auth-input <?= isset($errors['email']) && $mode === 'register' ? 'is-invalid' : '' ?>"
                        type="email" id="reg_email" name="email" autocomplete="email"
                        value="<?= $mode === 'register' ? $oldEmail : '' ?>">
                    <p class="auth-error"><?= $mode === 'register' ? $err('email') : '' ?></p>
                </div>

                <div class="auth-field">
                    <label for="reg_password">Password</label>
                    <div class="auth-input-wrap">
                        <input class="auth-input <?= isset($errors['password']) && $mode === 'register' ? 'is-invalid' : '' ?>"
                            :type="showPassword ? 'text' : 'password'" id="reg_password" name="password"
                            autocomplete="new-password" x-model="password" @input="onPasswordInput()">
                        <button type="button" class="auth-inline-btn" @click="showPassword = !showPassword"
                            x-text="showPassword ? 'Hide' : 'Show'">Show</button>
                    </div>
                    <!-- Live strength meter — the gradient signature as a fill. -->
                    <div class="pw-meter" x-show="password.length > 0" x-cloak>
                        <div class="pw-meter__track">
                            <div class="pw-meter__fill" :style="`width: ${strength.percent}%`"></div>
                        </div>
                        <p class="pw-meter__label" x-text="strength.label"></p>
                    </div>
                    <p class="auth-error"><?= $mode === 'register' ? $err('password') : '' ?></p>
                    <button type="button" class="auth-gen" @click="generate()">
                        &#10022; Generate a strong password
                    </button>
                </div>

                <div class="auth-field">
                    <label for="reg_confirm">Confirm password</label>
                    <input class="auth-input <?= isset($errors['confirm_password']) && $mode === 'register' ? 'is-invalid' : '' ?>"
                        :type="showPassword ? 'text' : 'password'" id="reg_confirm" name="confirm_password"
                        autocomplete="new-password" x-model="confirm">
                    <p class="auth-error"><?= $mode === 'register' ? $err('confirm_password') : '' ?></p>
                </div>

                <button class="auth-submit" type="submit">Create account</button>
                <p class="auth-switch">Already have an account?
                    <button type="button" @click="mode = 'login'">Log in</button>
                </p>
            </form>
        </div>
    </div>

    <style>[x-cloak]{display:none!important}</style>
</body>

</html>
