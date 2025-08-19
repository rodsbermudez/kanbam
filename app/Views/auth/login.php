<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kanban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        body { display: flex; align-items: center; justify-content: center; background-color: #212529; }
        .login-card { width: 100%; max-width: 400px; }
    </style>
</head>
<body>
<div class="card login-card shadow-lg">
    <div class="card-body p-4 p-md-5">
        <h3 class="card-title text-center mb-4">Acesso ao Kanban</h3>

        <?php if (session()->get('success')): ?>
            <div class="alert alert-success" role="alert"><?= session()->get('success') ?></div>
        <?php endif; ?>

        <?php if (session()->get('error')): ?>
            <div class="alert alert-danger" role="alert"><?= session()->get('error') ?></div>
        <?php endif; ?>

        <?php if (session()->get('errors')): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                <?php foreach (session()->get('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>

        <?= form_open('login/attempt') ?>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        <?= form_close() ?>
    </div>
</div>
</body>
</html>