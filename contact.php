<?php 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Trim es una unción de php que quita todo el whitespace antes y después de una entrada
        // respeta el espacio entre palabras, solo quita espacios, tabs y enters al inicio y al final.
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);


    // Validación básica de los datos de la forma de contacto.

    if ($name == "" OR $email == "" OR $message == "") {
        echo "You must specify a value for name, email address, and message.";
        exit;
    }

    // Code snippet dado por nyphp para prevenir ataque "header injection exploit"
        // Verifica cada elemento de la variable post por información maliciosa
    foreach( $_POST as $value ){
        if( stripos($value,'Content-Type:') !== FALSE ){
            echo "There was a problem with the information you entered.";    
            exit;
        }
    }

    // Complemento de defensa contra ataque tipo 1: inundación de correos
        // se complementa con un campo ficticio (ver más abajo)
        // Si el campo no está vacío es porque un robot lo llenó.
    if ($_POST["address"] != "") {
        echo "Your form submission has an error.";
        exit;
    }

    // Fin validación


    // Inicio de proceso para enviar e-mail

        // Incluir libreria 3rd party
    require_once("inc/phpmailer/class.phpmailer.php");
    $mail = new PHPMailer();

        // Método del objeto $mail que valida si la dirección de correo es valida
        // viene en la librería
    if (!$mail->ValidateAddress($email)){
        echo "You must specify a valid email address.";
        exit;
    }    

    $email_body = "";
    $email_body = $email_body . "Name: " . $name . "<br>"; // Se colocan HTML breack tags porque el cuerpo del correo es HTML
    $email_body = $email_body . "Email: " . $email . "<br>";
    $email_body = $email_body . "Message: " . $message;

        // Dice de parte de quién llega el correo
    $mail->SetFrom($email, $name); 

        // Hacia quién va el correo
    $address = "orders@shirts4mike.com";
    $mail->AddAddress($address, "Shirts 4 Mike");

        // Subject del correo.  Se concatena el nombre de la persona que envía para identificar el correo
        // esto evita que nuestro correo agrupe todos los correos en una misma conversacion debido a que tienen
        // diferentes subjects
    $mail->Subject    = "Shirts 4 Mike Contact Form Submission | " . $name;

        // Este metodo crea el cuerpo del correo con la infromación guardada en la variable $email_body
    $mail->MsgHTML($email_body);

        // Envío de correo
        //  Primero se ejecuta el método de envío de correo
        // $mail->Send() devuelve true si el correo pudo ser enviado con éxito
        // Error Info es una propiedad que guarda la informacion del error si la hay
    if(!$mail->Send()) {
      echo "There was a problem sending the email: " . $mail->ErrorInfo;
      exit;
    }

    header("Location: contact.php?status=thanks");
    exit;
    // Fin proceso para enviar e-mail
}
?>

<?php 
$pageTitle = "Contact Mike";
$section = "contact";
include('inc/header.php'); ?>

    <div class="section page">

        <div class="wrapper">

            <h1>Contact</h1>

            <?php if (isset($_GET["status"]) AND $_GET["status"] == "thanks") { ?>
                <p>Thanks for the email! I&rsquo;ll be in touch shortly!</p>
            <?php } else { ?>

                <p>I&rsquo;d love to hear from you! Complete the form to send me an email.</p>

                <form method="post" action="contact.php">

                    <table>
                        <tr>
                            <th>
                                <label for="name">Name</label>
                            </th>
                            <td>
                                <input type="text" name="name" id="name">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="email">Email</label>
                            </th>
                            <td>
                                <input type="text" name="email" id="email">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="message">Message</label>
                            </th>
                            <td>
                                <textarea name="message" id="message"></textarea>
                            </td>
                        </tr> 

                        <!-- Defensa contra ataque de spam tipo 1: inundación de correos -->
                            <!-- Técnica Span Honeypot field -->
                            <!-- Se complementa con un if en la parte de arriba -->
                            <!-- En este caso el css que esconde este campo está inline -->
                        <tr style="display: none;">
                            <th>
                                <!-- Es importante darle un nombre creíble para que el robot caiga -->
                                <label for="address">Address</label>
                            </th>
                            <td>
                                <input type="text" name="address" id="address">
                                
                                <!-- Es poco probable, pero puede pasar que el css no cargue y que un visitante
                                legítimo visite el sitio.  Por eso deje un mensaje para indicar que el campo se
                                debe dejar en blanco -->
                                <p>Humans (and frogs): please leave this field blank.</p>
                            </td>
                        </tr> 
                        <!-- Fin Span honey pot defense -->

                    </table>
                    <input type="submit" value="Send">

                </form>

            <?php } ?>

        </div>

    </div>

<?php include('inc/footer.php') ?>