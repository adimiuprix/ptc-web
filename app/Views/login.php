<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Form login</title>
</head>
<body>
    <div class="container pt-5">
        <form action="<?= base_url('auth');?>" method="post">
            <div class="mb-3">
                <input class="form-control" type="text" name="username" placeholder="username">
            </div>
            <div class="mb-3">
                <input class="form-control" type="password" name="password" placeholder="password">
            </div>
            <div class="text-center">
                <button class="btn btn-primary" type="submit">login</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>