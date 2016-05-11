<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace module;

use library\Module;

/**
 * Description of Commands
 *
 * @author ASUS
 */
class Commands extends Module
{
    const
        TRIGGER_ADD = 'add',
        TRIGGER_DELETE = 'del',
        TRIGGER_EDIT = 'edit';

    public function loadSettings($object = null)
    {
        $this->setCommand([
            'trigger' => 'cmd',
            'action' => 'trigger',
            'arguments' => -1,
            'permit' => true
        ]);

        $this->setCommand([
            'trigger' => 'bye',
            'action' => 'quit',
            'permit' => true
        ]);
        $this->setCommand([
            'trigger' => 'restart',
            'action' => 'restart',
            'permit' => true
        ]);
        $this->setCommand([
            'arguments' => -1,
            'trigger' => 'say',
            'action' => 'say',
            'permit' => true
        ]);
        $this->setCommand([
            'arguments' => 1,
            'trigger' => 'part',
            'action' => 'part',
            'help' => 'Type "!part #<channel>" for part a channel.',
            'permit' => true
        ]);
        $this->setCommand([
            'arguments' => 1,
            'trigger' => 'join',
            'action' => 'join',
            'help' => 'Type "!join #<channel>" for join to channel.',
            'permit' => true
        ]);
        $this->setCommand([
            'arguments' => -1,
            'trigger' => 'help',
            'action' => 'help',
            'help' => 'Type "!help" for help or "!help <command>" for help for command.',
            'permit' => false
        ]);
        parent::loadSettings($this);
    }

    protected function quit()
    {
        $this->reply('bye bye');
        $this->message('Sayounara', null, 'QUIT', true);
        exit();
    }

    protected function restart()
    {
        // Exit from Sever
        $this->message('I will be back', null, 'QUIT', true);
        // Reconnect to Server
        $this->bot->connectToServer();
    }

    protected function say(array $arguments = [])
    {
        if (!$arguments) {
            return;
        }
        $target = $arguments[0];
        unset($arguments[0]);
        $this->message(implode(' ', $arguments), $target);
    }

    protected function part(array $arguments)
    {
        $channel = $arguments[0];

        if (strpos($channel, '#') !== 0) {
            $channel = '#' . $channel;
        }

        $this->message('Cya, nerds', $channel, 'PART');
    }

    protected function join(array $arguments)
    {
        $channel = $arguments[0];
        if (strpos($channel, '#') !== 0) {
            $channel = '#' . $channel;
        }
        $this->message($channel, null, 'JOIN');
    }

    protected function help(array $arguments = [])
    {
        $commandList = [];
        foreach ($this->bot->module as $module) {
            if (!$module->commands) {
                continue;
            }
            foreach ($module->commands as $name => $command) {
                if ($command['permit']) {
                    continue;
                }
                if (!$this->checkCommand($command, Module::CHECK_CHAN)) {
                    continue;
                }
                if (!empty($arguments[0])) {
                    if ($arguments[0] == $name) {
                        return $this->reply(($command['help']) ? $command['help'] : "Just type \"!{$command['trigger']}\"");
                    }
                } else {
                    $commandList[] = $command['trigger'];
                }
            }
        }
        if ($commandList) {
            $this->reply('I\'m a spy so I know some commands. "!' . implode('"; "!', $commandList) . '";');
            $this->reply('Type "!help <command>" for help');
        }
    }

    protected function trigger(array $arguments = [])
    {
        $mode = trim($arguments[0]);
        $parsedArguments = [];
        if ($mode != self::TRIGGER_DELETE) {
            foreach ($arguments as $argument) {
                if (strpos($argument, ':') === false) {
                    $parsedArguments[] = trim($argument);
                } else {
                    $column = explode(':', $argument, 2);
                    $parsedArguments[trim($column[0])] = trim($column[1]);
                }
            }
            $parsedArguments['trigger'] = isset($parsedArguments['trigger']) ? $parsedArguments['trigger'] : null;
            $parsedArguments['channels'] = isset($parsedArguments['channels']) ? explode(',', $parsedArguments['channels']) : null;
            $parsedArguments['reply'] = isset($parsedArguments['reply']) ? str_replace('_', ' ', $parsedArguments['reply']) : null;
            $parsedArguments['notice'] = isset($parsedArguments['notice']) ? str_replace('_', ' ', $parsedArguments['notice']) : null;
            $parsedArguments['regex'] = isset($parsedArguments['regex']) ? $parsedArguments['regex'] : null;
            $parsedArguments['nick'] = isset($parsedArguments['nick']) ? $parsedArguments['nick'] : null;
            $parsedArguments['name'] = isset($parsedArguments['name']) ? $parsedArguments['name'] : null;
            $parsedArguments['host'] = isset($parsedArguments['host']) ? $parsedArguments['host'] : null;

            //array_map('trim', $arguments['channels']);
            $command = [
                'trigger' => (string) $parsedArguments['trigger'],
                'reply' => (string) $parsedArguments['reply'],
                'notice' => (string) $parsedArguments['notice'],
                'channels' => (array) $parsedArguments['channels'],
                'ban' => [
                    'regex' => (string) $parsedArguments['regex'],
                    'nick' => (string) $parsedArguments['nick'],
                    'name' => (string) $parsedArguments['name'],
                    'host' => (string) $parsedArguments['host']
                ]
            ];
        }
        switch ($mode) {
            case self::TRIGGER_ADD:
                if (empty($parsedArguments['trigger'])) {
                    return $this->reply('You shall type a trigger.');
                }
                if (empty($parsedArguments['reply']) && empty($parsedArguments['notice'])) {
                    return $this->reply('You shall type reply message.');
                }
                if ($this->setCommand($command)) {
                    $this->reply("Command {$command['trigger']} was loaded.");
                } else {
                    $this->reply("Command {$command['trigger']} exists.");
                }
                break;
            case self::TRIGGER_DELETE:
                if (isset($arguments[1])) {
                    $name = trim(strtolower($arguments[1]));
                    if ($this->unsetCommand($name)) {
                        return $this->reply("Command {$name} was deleted.");
                    } else {
                        return $this->reply("Command {$name} not found.");
                    }
                } else {
                    return $this->reply("Must type a name of command.");
                }
            case self::TRIGGER_EDIT:
                if (empty($parsedArguments['trigger'])) {
                    return $this->reply('You shall type a trigger.');
                }
                return $this->reply('Option not avaible.');
            default:
                return false;
        }
    }

    public function propertyToSave()
    {
        return [
            'commands' => $this->commands
        ];
    }
}
