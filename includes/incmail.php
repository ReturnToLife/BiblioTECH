<?php
// +----------------------------------------------------------------------+
// | php version 4                                                        |
// +----------------------------------------------------------------------+
// | copyright (c) 1997-2003 the php group                                |
// +----------------------------------------------------------------------+
// | this source file is subject to version 2.02 of the php license,      |
// | that is bundled with this package in the file license, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | if you did not receive a copy of the php license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | author: chuck hagenbuch                             |
// +----------------------------------------------------------------------+
//
// $id: mail.php,v 1.6 2003/06/26 07:05:36 jon exp $

require_once 'pear.php';

/**
 * pear's mail:: interface. defines the interface for implementing
 * mailers under the pear hierarchy, and provides supporting functions
 * useful in multiple mailer backends.
 *
 * @access public
 * @version $revision: 1.6 $
 * @package mail
 */
class mail
{
    /**
     * line terminator used for separating header lines.
     * @var string
     */
    var $sep = "
";

    /**
     * provides an interface for generating mail:: objects of various
     * types
     *
     * @param string $driver the kind of mail:: object to instantiate.
     * @param array  $params the parameters to pass to the mail:: object.
     * @return object mail a instance of the driver class or if fails a pear error
     * @access public
     */
    function factory($driver, $params = array())
    {
        $driver = strtolower($driver);
        @include_once 'mail/' . $driver . '.php';
        $class = 'mail_' . $driver;
        if (class_exists($class)) {
            return new $class($params);
        } else {
            return pear::raiseerror('unable to find class for driver ' . $driver);
        }
    }

    /**
     * implements mail::send() function using php's built-in mail()
     * command.
     *
     * @param mixed $recipients either a comma-seperated list of recipients
     *              (rfc822 compliant), or an array of recipients,
     *              each rfc822 valid. this may contain recipients not
     *              specified in the headers, for bcc:, resending
     *              messages, etc.
     *
     * @param array $headers the array of headers to send with the mail, in an
     *              associative array, where the array key is the
     *              header name (ie, 'subject'), and the array value
     *              is the header value (ie, 'test'). the header
     *              produced from those values would be 'subject:
     *              test'.
     *
     * @param string $body the full text of the message body, including any
     *               mime parts, etc.
     *
     * @return mixed returns true on success, or a pear_error
     *               containing a descriptive error message on
     *               failure.
     * @access public
     * @deprecated use mail_mail::send instead
     */
    function send($recipients, $headers, $body)
    {
        // if we're passed an array of recipients, implode it.
        if (is_array($recipients)) {
            $recipients = implode(', ', $recipients);
        }

        // get the subject out of the headers array so that we can
        // pass it as a seperate argument to mail().
        $subject = '';
        if (isset($headers['subject'])) {
            $subject = $headers['subject'];
            unset($headers['subject']);
        }

        // flatten the headers out.
        list(,$text_headers) = mail::prepareheaders($headers);

        return mail($recipients, $subject, $body, $text_headers);

    }

    /**
     * take an array of mail headers and return a string containing
     * text usable in sending a message.
     *
     * @param array $headers the array of headers to prepare, in an associative
     *              array, where the array key is the header name (ie,
     *              'subject'), and the array value is the header
     *              value (ie, 'test'). the header produced from those
     *              values would be 'subject: test'.
     *
     * @return mixed returns false if it encounters a bad address,
     *               otherwise returns an array containing two
     *               elements: any from: address found in the headers,
     *               and the plain text version of the headers.
     * @access private
     */
    function prepareheaders($headers)
    {
        $lines = array();
        $from = null;

        foreach ($headers as $key => $value) {
            if ($key === 'from') {
                include_once 'mail/rfc822.php';

                $addresses = mail_rfc822::parseaddresslist($value, 'localhost',
                                                    false);
                $from = $addresses[0]->mailbox . '@' . $addresses[0]->host;

                // reject envelope from: addresses with spaces.
                if (strstr($from, ' ')) {
                    return false;
                }

                $lines[] = $key . ': ' . $value;
            } elseif ($key === 'received') {
                // put received: headers at the top.  spam detectors often
                // flag messages with received: headers after the subject:
                // as spam.
                array_unshift($lines, $key . ': ' . $value);
            } else {
                $lines[] = $key . ': ' . $value;
            }
        }

        return array($from, join($this->sep, $lines) . $this->sep);
    }

    /**
     * take a set of recipients and parse them, returning an array of
     * bare addresses (forward paths) that can be passed to sendmail
     * or an smtp server with the rcpt to: command.
     *
     * @param mixed either a comma-seperated list of recipients
     *              (rfc822 compliant), or an array of recipients,
     *              each rfc822 valid.
     *
     * @return array an array of forward paths (bare addresses).
     * @access private
     */
    function parserecipients($recipients)
    {
        include_once 'mail/rfc822.php';

        // if we're passed an array, assume addresses are valid and
        // implode them before parsing.
        if (is_array($recipients)) {
            $recipients = implode(', ', $recipients);
        }

        // parse recipients, leaving out all personal info. this is
        // for smtp recipients, etc. all relevant personal information
        // should already be in the headers.
        $addresses = mail_rfc822::parseaddresslist($recipients, 'localhost', false);
        $recipients = array();
        if (is_array($addresses)) {
            foreach ($addresses as $ob) {
                $recipients[] = $ob->mailbox . '@' . $ob->host;
            }
        }

        return $recipients;
    }

}
?>