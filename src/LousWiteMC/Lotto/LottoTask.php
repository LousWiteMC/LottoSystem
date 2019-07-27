<?php

declare(strict_types=1);

namespace LousWiteMC\Lotto;

use pocketmine\scheduler\Task;
use LousWiteMC\Lotto\Lotto;
use pocketmine\item\Item;
use onebone\economyapi\EconomyAPI;

class LottoTask extends Task{

	/** @var int $seconds */
	private $seconds = 0;

	public function onRun(int $tick) : void{
		$this->seconds++;
		$time = intval(Lotto::getInstance()->getSetting("Lotto-Time-Minutes")) * 60;
		$startTime = $time - $this->seconds;
		switch($startTime){
			case 100:
				Lotto::getInstance()->getServer()->broadcastMessage(Lotto::getInstance()->getSetting("Lotto-Starting-In-2-Minutes"));
				return;
			case 50:
				Lotto::getInstance()->getServer()->broadcastMessage(Lotto::getInstance()->getSetting("Lotto-Starting-In-1-Minutes"));
				return;
			case 5:
				Lotto::getInstance()->getServer()->broadcastMessage(Lotto::getInstance()->getSetting("Lotto-Starting-In-5-Seconds"));
				return;
			case 4:
				Lotto::getInstance()->getServer()->broadcastMessage(Lotto::getInstance()->getSetting("Lotto-Starting-In-4-Seconds"));
				return;
			case 3:
				Lotto::getInstance()->getServer()->broadcastMessage(Lotto::getInstance()->getSetting("Lotto-Starting-In-3-Seconds"));
				return;
			case 2:
				Lotto::getInstance()->getServer()->broadcastMessage(Lotto::getInstance()->getSetting("Lotto-Starting-In-2-Seconds"));
				return;
			case 1:
				Lotto::getInstance()->getServer()->broadcastMessage(Lotto::getInstance()->getSetting("Lotto-Starting-In-1-Seconds"));
				return;
			case 0:
				foreach(Lotto::getInstance()->getServer()->getOnlinePlayers() as $player){
					$item = Item::get(Lotto::getInstance()->getSetting("ID-Item-Ticket"),Lotto::getInstance()->getSetting(("ID-Meta-Ticket")));
					$nameitem = $item->getCustomName();
					$random = Lotto::getInstance()->getRandomNumber();
					$inv = $player->getInventory();
					if(!($inv->contains($item))){
						Lotto::getInstance()->getServer()->getOnlinePlayers()->sendMessage("§eLotto System\n§cNobody Had Won On This Lottery");
					}
					if($nameitem(in_array(implode($random)))){
						$reward = mt_rand(1,5);
						switch($reward){
							case 1:
							$reward = 100000;
							return;
							case 2:
							$reward = 75000;
							return;
							case 3:
							$reward = 50000;
							return;
							case 4:
							$reward = 35000;
							return;
							case 5:
							$reward = 20000;
							return;
						}
						$player->sendMessage("§aYou Won, Earned ".$reward."$");
						EconomyAPI::getInstance()->giveMoney($player, $reward);
						Lotto::getInstance()->getServer()->getOnlinePlayers()->sendMessage("§eLotto System\n§a- Player ".$player->getName()." Has Won On This Lottery!");
				}
			}
		}
	}
}