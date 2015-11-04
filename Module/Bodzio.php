<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Module;

/**
 * Description of Bodzio
 *
 * @author ASUS
 */
class Bodzio extends \Library\Module
{
    private $kierCount = 0;

    public function loadSettings($object = null)
    {
        $this->setCommand([
            'trigger' => 'okusama04',
            'channels' => ['#bodzio'],
            'notice' => 'prosze: https://mega.co.nz/#!HplkQaCZ!gTgt_BjzpEoUe6-5_ObXke_PDM-S9Me6xz8aW_cVD0A'
        ]);

        $this->setCommand([
            'trigger' => 'hubi1',
            'channels' => ['#bodzio'],
            'reply' => '[10:31pm] <+hubi1> k-on byl fajny'
        ]);

        $this->setCommand([
            'trigger' => 'iroha',
            'action' => 'iroha',
            'arguments' => 1,
            'channels' => ['#bodzio', '#xandros'],
            'help' => 'Sending a link to episode. Just type "!iroha <nr of episode>" to get link.'
        ]);

        $this->setCommand([
            'trigger' => 'kier',
            'channels' => ['#bodzio', '#xandros'],
            'action' => 'kier'
        ]);

        $this->setCommand([
            'trigger' => 'okurwa',
            'notice' => 'HDTV: https://mega.nz/#!q10UxRzR!QGua8AijnINhJWnZMSSlEIfdHLh13s_7cBiYqbBQU04 BD: https://mega.nz/#!39tyUCqZ!7hMUp_uRzVlmVR6UgBFH0fI5NxCguWtSuHKkiw_DMqE',
            'channels' => ['#bodzio']
        ]);

        parent::loadSettings($this);
    }

    protected function iroha(array $arguments)
    {
        parent::RedBeanConnect('xandrosmaker_cba_pl');
        $relese = R::findLast('relki', 'where `seria` LIKE ? AND  `ep` = ?', ['%Iroha%', (int) $arguments[0]]);
        $message = ($relese) ? 'Prosze: ' . $relese->link : 'Jeszcze jej nie mam, ale lap w zamian ladny ending: http://exsubs.anidb.pl/?wpfb_dl=28';
        $this->message($message, $this->bot->getUserNick(), IRC::NOTICE);
        R::close();
    }

    protected function kier()
    {
        if ($this->kierCount == 3) {
            $this->kierCount = 0;
        }
        $text = [
            '[10:52pm] <KieR> robotic notes bylo niezle',
            '[3:34pm] <KieR> mi sie w portalu podobalo sikanie na ludzi xD',
            '[7:56pm] <KieR> grac we wladce [7:56pm] <KieR> a nie jakies gowna xD',
        ];
        $this->reply($text[$this->kierCount++]);
    }
}
