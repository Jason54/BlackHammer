<?php

namespace BlackHammer;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\Pickaxe;
use pocketmine\item\TieredTool;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class Hammer extends PluginBase implements Listener
{
    private $config;
    private $olditem;

    public function onEnable()
    {
	    $this->getServer()->getPluginManager()->registerEvents($this, $this);

        @mkdir($this->getDataFolder());

        if(!file_exists($this->getDataFolder()."config.yml")){

            $this->saveResource('config.yml');

        }

        $this->config = new Config($this->getDataFolder().'config.yml', Config::YAML);

        ItemFactory::registerItem(new Pickaxe($this->config->get("id"),0,"Hammer", TieredTool::TIER_DIAMOND), true);
        Item::initCreativeItems();
    }

    public function onBlockBreaks(BlockBreakEvent $event)
    {
        $item = $event->getItem();
        $block = $event->getBlock();

        if ($item->getId() === $this->config->get("id")) {

            if (!$event->isCancelled()) {

                $event->setCancelled();
                $this->addBlock($block);

            }
        }

    }

    private function addBlock(Block $blocks)
    {
        $minX = $blocks->x - 1;
        $maxX = $blocks->x + 1;

        $minY = $blocks->y - 1;
        $maxY = $blocks->y + 1;

        $minZ = $blocks->z - 1;
        $maxZ = $blocks->z + 1;

        for ($x = $minX; $x <= $maxX; $x++) {

            for ($y = $minY; $y <= $maxY; $y++) {

                for ($z = $minZ; $z <= $maxZ; $z++) {

                    $block = $blocks->getLevel()->getBlockAt($x,$y,$z);
                    $item = Item::get(Item::IRON_PICKAXE);
                    $block->getLevel()->useBreakOn($block,$item);

                }

            }

        }
    }

}
