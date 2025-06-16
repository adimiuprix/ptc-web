<!DOCTYPE html>
<html lang="en">

<head>
    <title>PTC Iklan</title>
    <!-- App favicon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?= site_url('public/app.min.css') ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?= site_url('public/styles.css') ?>" rel="stylesheet" type="text/css" />

</head>


<body data-sidebar="dark" style="overflow: hidden">

    <div class="container-fluid ptc-header">
        <div class="row">
            <div class="col-md-6">
                <a class="btn btn-success" href="<?= site_url('/ptc') ?>">Back to home</a>
            </div>
            <div class="col-md-6">
                <span class="font-size-15 text-truncate text-white" id="ptcCountdown">Please wait...</span>
            </div>
        </div>
    </div>
    <iframe id="ads" src="<?= $ads['url'] ?>" frameborder="0" style="width: 100%; height: calc(100vh - 75px);" sandbox="allow-same-origin allow-scripts allow-forms"></iframe>
    
    <div class="modal fade" id="ptcCaptcha" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan captcanya cuk!!!</h5>
                </div>
                <div class="modal-body">
                    <form action="<?= site_url('/ptc/verify/') ?>" method="POST">
                        <center>
                            <!-- captcha display -->
                        </center>
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="user_token">
                        <button id="verify" class="btn btn-success btn-block" type="submit">Verify</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>

    <script>
    const timer = <?= $ads['timer'] ?>;
    const url = '<?= $ads['url'] ?>';
    let countdown = timer - 1;

    $(() => {
        const $selectCaptcha = $('#selectCaptcha');
        const $ptcCountdown = $('#ptcCountdown');

        // Tampilkan captcha pertama
        const current = $selectCaptcha.val();
        $(`#${current}`).show();

        // Countdown logic
        const count = setInterval(() => {
            if (countdown < 0) {
                $('#ptcCaptcha').modal('show');
                clearInterval(count);
                return;
            }

            const label = countdown === 1 ? 'second' : 'seconds';
            $ptcCountdown.text(`${countdown} ${label}`);

            if (document.hasFocus())
                countdown--
        }, 1000)

        // Klik tombol verify
        $('#verify').on('click', () => {
            const win = window.open(url, '_blank');
            if (win) win.focus();
        })
    })
    </script>

    <?php if (isset($_COOKIE['captcha'])) { ?>
    <script>
        $('option[value=<?= $_COOKIE['captcha'] ?>]').attr('selected', 'selected');
    </script>
    <?php } ?>
    <?php
    if (isset($_SESSION['sweet_message'])) {
        echo $_SESSION['sweet_message'];
    }
    ?>
</body>

</html>