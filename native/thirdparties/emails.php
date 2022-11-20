<?php

    namespace native\thirdparties;

    use native\libs\Options;
    use native\libs\Thirdparty;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use Throwable;

    class Emails extends Thirdparty {

        public function render_from_module(string $module, string $key, array $params = []) : string
        {
            ob_start();
            include DIR_APP . "/modules/" . $module . "/views/emails" . $key . ".php";
            $output = ob_get_clean();
            return $output;
        }

        public function render(string $key, array $params = []) : string
        {
            ob_start();
            include DIR_APP . '/views/emails' . $key . '.php';
            $output = ob_get_clean();
            return $output;
        }

        /**
         * @param {string} to The recipient email address
         * @param {string} subject The email's subject
         * @param {string} body The email's body (HTML code)
         * @return {boolean} TRUE when email was sent, FALSE when not
         */
        public function send(array $params) : bool {
            $mail = new PHPMailer(true);
            try {
                $mail->IsSMTP();
                $mail->Mailer = 'smtp';
                $mail->SMTPAuth = true;

                $mail->Host = Options::get('SMTP_HOST');
                $mail->SMTPSecure = Options::get('SMTP_SECURITY');
                $mail->Port = Options::get('SMTP_PORT');

                $mail->Username = Options::get('SMTP_USER');
                $mail->Password = Options::get('SMTP_PASS');

                $mail->IsHTML(true);

                if(!empty(Options::get('SMTP_FROM')))
                    $mail->From = Options::get('SMTP_FROM');
                else
                    $mail->From = Options::get('SMTP_USER');

                if(!empty(Options::get('SMTP_NAME')))
                    $mail->FromName = Options::get('SMTP_NAME');

                $mail->addAddress($params['to']);

                if(!empty($params['subject']))
                    $mail->Subject = $params['subject'];
                else {
                    preg_match("/\<title\>(.+)\<\/title\>/m", $params['body'], $matches);
                    $mail->Subject = $matches[1];
                }

                $mail->Body = $params['body'];
                $mail->CharSet = 'UTF-8';

                $mail->Send();
            } catch (Throwable $e) {
                return false;
            }
            return true;
        }
    }
