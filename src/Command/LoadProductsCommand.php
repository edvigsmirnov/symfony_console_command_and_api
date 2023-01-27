<?php

namespace App\Command;

use AllowDynamicProperties;
use App\Entity\Product;
use App\Services\XmlReader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AllowDynamicProperties] #[AsCommand(
    name: 'app:load-products',
    description: 'Loads a list of products from a file by the entered URL',
)]
class LoadProductsCommand extends Command
{
    use LockableTrait;

    private const PRODUCTS_NODE_NAME = 'products';
    private const PRODUCT_NODE_NAME = 'product';

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fileUrl', InputArgument::REQUIRED, 'URL to file with a list of products');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $output);

        $fileUrl = $input->getArgument('fileUrl');

        if ($fileUrl) {
            $io->note(sprintf('You passed an argument: %s', $fileUrl));
        }

        $products = $this->getProductsList($fileUrl);

        $item = $products->current();

        while ($item) {
            $this->saveProduct($item);
            $item = $products->next();
        }

        $io->success('Products were successfully loaded');

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     */
    private function getProductsList(string $fileUrl): XmlReader
    {
        $reader = new XmlReader($fileUrl);
        $reader->filterXmlByNodeAndChild(self::PRODUCTS_NODE_NAME, self::PRODUCT_NODE_NAME);

        return $reader;
    }

    private function saveProduct(SimpleXMLElement $item): void
    {
        $product = new Product();

        $product->setProductId((int)$item->product_id);
        $product->setTitle((string)$item->title);
        $product->setDescription((string)$item->description);
        $product->setInetPrice((int)$item->inet_price);
        $product->setPrice((int)$item->price);
        $product->setRating((int)$item->rating);
        $product->setImage((string)$item->image);

        $this->em->persist($product);

        $this->em->flush();
    }
}
