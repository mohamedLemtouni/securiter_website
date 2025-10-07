<!DOCTYPE html>
<html lang="fr">
<?php session_start(); ?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" sizes="192x192" href="img/logo_sans_fond.png">
    <link rel="stylesheet" href="/static/css/styleindex.css">
    <link rel="stylesheet" href="/static/css/styleContact.css">
    <link rel="stylesheet" href="util.css">
</head>

    <?php include("header.php");?>
<body>
    
    <div class="bg-contact2" style="background-image: url('images/bg-01.jpg');">
        <div class="container-contact2">
            <div class="wrap-contact2">
                <form class="contact2-form validate-form" action="./traitement.php?value=mail" method="post">
                    <span class="contact2-form-title">
                        Nous contacter...
                    </span>

                    <div class="wrap-input2 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                        <input class="input2" type="email" name="maildest" id="maildest">
                        <span class="focus-input2" data-placeholder="E-MAIL"></span>
                    </div>
                    <div class="wrap-input2 validate-input">
                        <input class="input2" type="text" name="objectmail" id="objectmail">
                        <span class="focus-input2" data-placeholder="OBJET"></span>
                    </div>
                    <div class="wrap-input2 validate-input" data-validate="Message is required">
                        <textarea class="input2" name="message" id="message"></textarea>
                        <span class="focus-input2" data-placeholder=" VOTRE MESSAGE ICI"></span>
                    </div>
                    <div class="container-contact2-form-btn">
                        <div class="wrap-contact2-form-btn">
                            <div class="contact2-form-bgbtn"></div>
                            <button class="contact2-form-btn" type="submit">
                                Envoyer Votre Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>

    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <script src="vendor/select2/select2.min.js"></script>

    <script src="js/main.js"></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-23581568-13');
    </script>
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/v8b253dfea2ab4077af8c6f58422dfbfd1689876627854" integrity="sha512-bjgnUKX4azu3dLTVtie9u6TKqgx29RBwfj3QXYt5EKfWM/9hPSAI/4qcV5NACjwAo8UtTeWefx6Zq5PHcMm7Tg==" data-cf-beacon='{"rayId":"806eef6409473b5e","token":"cd0b4b3a733644fc843ef0b185f98241","version":"2023.8.0","si":100}' crossorigin="anonymous"></script>


</body>
<?php include("footer.php"); ?>

</html>
