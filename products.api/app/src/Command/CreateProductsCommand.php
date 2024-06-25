<?php

namespace App\Command;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'create:products'
)]
class CreateProductsCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly EntityManagerInterface $entityManager,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->fetchBrands() as $brand) {
            foreach ($this->fetchPhones($brand->brand_slug) as $phone) {
                $details = $this->phoneDetails($phone->slug);
                try {
                    $product = new Products();
                    $product
                        ->setBrand($details->brand)
                        ->setImgUrl($details->thumbnail)
                        ->setDesignation($details->phone_name)
                        ->setPrice(rand(10000, 150000))
                        ->setDescription($details->release_date)
                        ->setColor($this->getColor($details->specifications))
                        ->setStorage($details->storage)
                        ->setBackCameraResolution($this->getBackCamera($details->specifications))
                        ->setFrontCameraResolution($this->getFrontCamera($details->specifications))
                        ->setRam($this->getMemory($details->specifications))
                        ->setResolution($this->getResolution($details->specifications))
                        ->setProcessor($this->getProcessor($details->specifications))
                        ->setStocks(rand(0, 500))
                    ;

                    $this->entityManager->persist($product);
                    $this->entityManager->flush();
                    $output->writeln(
                        sprintf('Inserted %s', $details->phone_name)
                    );
                } catch (\Exception $ex) {
                    $output->writeln(
                        sprintf('%s', $ex->getMessage())
                    );
                }

                sleep(1);
            }
            sleep(10);
        }

        return Command::SUCCESS;
    }

    private function getProcessor($specifications): string
    {
        foreach ($specifications as $specification) {
            if ($specification->title === 'Platform') {
                foreach ($specification->specs as $spec) {
                    if ($spec->key === 'CPU') {
                        return $spec->val[0];
                    }
                }
            }
        }

        return '';
    }

    private function getFrontCamera($specifications): string
    {
        foreach ($specifications as $specification) {
            if ($specification->title === 'Selfie camera') {
                foreach ($specification->specs as $spec) {
                    if ($spec->key === 'Single') {
                        return $spec->val[0];
                    }
                }
            }
        }

        return '';
    }

    private function getMemory($specifications): string
    {
        foreach ($specifications as $specification) {
            if ($specification->title === 'Memory') {
                foreach ($specification->specs as $spec) {
                    if ($spec->key === 'Internal') {
                        return $spec->val[0];
                    }
                }
            }
        }

        return '';
    }

    private function getBackCamera($specifications): string
    {
        foreach ($specifications as $specification) {
            if ($specification->title === 'Main Camera') {
                foreach ($specification->specs as $spec) {
                    if ($spec->key === 'Single') {
                        return $spec->val[0];
                    }
                }
            }
        }

        return '';
    }

    private function getResolution($specifications): string
    {
        foreach ($specifications as $specification) {
            if ($specification->title === 'Display') {
                foreach ($specification->specs as $spec) {
                    if ($spec->key === 'Resolution') {
                        return $spec->val[0];
                    }
                }
            }
        }

        return '';
    }

    private function getColor($specifications): string
    {
        foreach ($specifications as $specification) {
            if ($specification->title === 'Misc') {
                foreach ($specification->specs as $spec) {
                    if ($spec->key === 'Colors') {
                        return $spec->val[0];
                    }
                }
            }
        }

        return '';
    }

    private function fetchBrands(): array
    {
        $req = $this->client->request(
            'GET',
            'https://phone-specs-api-2.azharimm.dev/brands'
        );

        if ($req->getStatusCode() === 200) {
            $data = json_decode($req->getContent());
            return $data->data;
        }

        throw new \Exception();
    }

    private function fetchPhones(string $brandSlug): array
    {
        $req = $this->client->request(
            'GET',
            'https://phone-specs-api-2.azharimm.dev/brands/' . $brandSlug
        );

        if ($req->getStatusCode() === 200) {
            $data = json_decode($req->getContent());
            return $data->data->phones;
        }

        throw new \Exception();
    }

    private function phoneDetails(string $phoneSlug): object
    {
        $req = $this->client->request(
            'GET',
            'https://phone-specs-api-2.azharimm.dev/' . $phoneSlug
        );

        if ($req->getStatusCode() === 200) {
            $data = json_decode($req->getContent());
            return $data->data;
        }

        throw new \Exception();
    }
}