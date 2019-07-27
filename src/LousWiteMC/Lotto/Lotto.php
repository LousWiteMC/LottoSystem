<?php

namespace LousWiteMC\Lotto;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\command\{CommandSender, Command};
use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use LousWiteMC\Lotto\LottoTask;

class Lotto extends PluginBase implements Listener{
	
	private static $instance;
	
	public function onEnable() : void{
		self::$instance = $this;
		$this->saveResource("settings.yml");
		@mkdir($this->getDataFolder());
		$this->settings = new Config($this->getDataFolder(). 'settings.yml', Config::YAML);
		$this->getScheduler()->scheduleRepeatingTask(new LottoTask(), 20);
		$this->getLogger()->info("§aLottoSystem (Vé Số System) By LousWiteMC Enabled!");
		$this->eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
	}

	public static function getInstance() : self{
		return self::$instance;
	}

	public function getSetting($st){
		return $this->settings->get($st);
	}
	
	public function giveTicket($player, $amount){
		$item = Item::get($this->getSetting("ID-Item-Ticket"), $this->getSetting("ID-Meta-Ticket"), $amount);
		$so = str_replace("{NumberLotto}", $this->getRandomNumber(), $this->getSetting("CustomName-Ticket"));
		$item->setCustomName($so);
		$item->setLore(array($this->getSetting("Lore-Ticket")));
		$item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(17), 1));
		$inv = $player->getInventory();
		$inv->addItem($item);
	}
	
	public function getTicketName(){
		$item = Item::get($this->getSetting("ID-Item-Ticket"), $this->getSetting("ID-Meta-Ticket"));
		$name = $item->getCustomName();
		return $name;
	}
	
	public function getTicket(){
		$item = Item::get($this->getSetting("ID-Item-Ticket"), $this->getSetting("ID-Meta-Ticket"));
		return $item;
	}
	
	public function getRandomNumber(){
		$number = mt_rand(100000, 999999);
		return $number;
	}
	
	public function onCommand(CommandSender $player, Command $cmd, string $label, array $args) : bool{
		if($cmd->getName() == "lotto"){
			if(isset($args[0])){
				if($args[0] == null && $args[0] == "help"){
					$msg = str_replace("\n", "\n", $this->settings->get("Msg-Help"));
					$player->sendMessage($msg);
				}
				if($args[0] == "buy"){
					if(isset($args[1])){
						if($args[1] == null){
							$msgs = str_replace("\n", "\n", $this->settings->get("Msg-Help-Command-Buy"));
							$player->sendMessage($msgs);
							return false;
						}
						if(!(is_numeric($args[1]))){
							$msgss = str_replace("\n", "\n", $this->settings->get("Msg-Amount-Must-Be-Numberic"));
							$player->sendMessage($msgss);
							return false;
						}
						$this->buyTicket($player, $args[1]);
						return true;
					}
				}
			}
		}
		return true;
	}
	
	public function buyTicket($player, $amount){
		$money = $this->eco->myMoney($player);
		$cost = $this->getSetting("Cost-One-Ticket")*$amount;
		if(!($money >= $cost)){
			$msgnomoney = str_replace("\n", "\n", $this->getSetting("Not-Have-Enough-Money-To-Buy"));
			$player->sendMessage($msgnomoney);
			return false;
		}
		$this->eco->reduceMoney($player, $cost);
		$this->giveTicket($player, $amount);
		return true;
	}
}