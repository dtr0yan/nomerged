<?php

require __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/config.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Process\Process;

/**
 * setup logger
 */
$log = new Logger($config['app']['name']);
$log->pushHandler(
    new StreamHandler($config['log']['path'] . '/' . date('Y-m-d') . '.log',
    $config['log']['level'])
);

/**
 * execute git command
 */
$process = new Process($config['gitBin'] . ' for-each-ref --format="%(committerdate) %09 %(authorname) %09 %(refname) %09 %(authoremail)" --no-merged ' . $config['trackedBranch']);
$process->setWorkingDirectory($config['workDir']);
$process->run();

/**
 * process data returned by git command
 */
$output = explode("\n", $process->getOutput());

$formattedResult = [];

$keys = ['createdAt', 'creatorName', 'branchName', 'creatorEmail'];

$dataToSend = [];

foreach ($output as $index => $item) {
    $explodedItem = explode("\t", $item);
    $explodedItem = array_map('trim', $explodedItem);

    if (count($explodedItem) == 4) {
        $formattedResult[$index] = array_combine($keys, $explodedItem);

        foreach ($config['users'] as $username => $userData) {
            $email = strtolower(trim($formattedResult[$index]['creatorEmail'], '<>'));
            if (in_array($email, $userData['emails'])) {
                $dataToSend[$username][] = $formattedResult[$index];
            }
        }
    }
}

/**
 * send email notifications
 */
$transport = Swift_SmtpTransport::newInstance(
    $config['app']['email']['smtpServer'],
    $config['app']['email']['port']
)
    ->setUsername($config['app']['email']['user'])
    ->setPassword($config['app']['email']['password']);

$mailer = Swift_Mailer::newInstance($transport);

foreach ($dataToSend as $username => $data) {
    /**
     * prepare message body
     */
    $messageBody = 'Hi, <strong>' . $config['users'][$username]['name'] . "!</strong><br><br>Please pay attention to next branches that not merged to development: <br><br>";
    $messageBody .= '<table><tr><td width="10%">#</td><th>Creation Date</th><th>Branch</th></tr>';
    foreach ($data as $index => $item) {
        $messageBody .= '<tr><td>' . $index . '</td><td>'. $item['createdAt'] . '</td><td>' . $item['branchName']  . '</td></tr>';
    }

    $messageBody .= '</table><br>';

    $messageBody .= 'Please review this branches and remove them or merge to development.<br>Or keep in mind that you have this branches not merged to development. <br><br>';
    $messageBody .= 'Thanks and have a nice day!';

    $message = Swift_Message::newInstance('Morning pre-work email')
        ->setFrom([$config['app']['email']['from'] => $config['app']['name']])
        ->setTo($config['users'][$username]['sendTo'])
        ->setContentType('text/html')
        ->setBody($messageBody);

    /**
     * send
     */
    try {
        if ($mailer->send($message) == 1) {
            $log->addInfo('Mail was sent to ' . $username);
        } else {
            $log->addError('Can\'t send to ' . $config['users'][$username]['sendTo']);
        }
    } catch (\Exception $e) {
        $log->addCritical($e->getMessage() . "\n" . $e->getTraceAsString());
    }
}