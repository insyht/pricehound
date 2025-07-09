<?php

use App\Livewire\Shop\Create as CreateShop;
use App\Models\Shop;
use function Pest\Livewire\livewire;

function generateFakeXPath(): string
{
    $elements = ['div', 'span', 'p', 'ul', 'li', 'section', 'article'];
    $attributes = ['id', 'class', 'data-test'];

    $depth = rand(2, 5);
    $xpath = '/html/body';

    for ($i = 0; $i < $depth; $i++) {
        $element = $elements[array_rand($elements)];
        if (rand(0, 1)) {
            $attr = $attributes[array_rand($attributes)];
            $value = fake()->word();
            $xpath .= "/{$element}[@{$attr}=\"{$value}\"]";
        } else {
            $xpath .= "/{$element}";
        }
    }

    return $xpath;
}

it('creates a new shop', function () {
    $shopName = fake()->word();
    $xPath = generateFakeXPath();

    livewire(CreateShop::class)
        ->set('name', $shopName)
        ->set('xpathPrice', $xPath)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('shops.index'));

    $createdShop = Shop::where('name', $shopName)->first();
    expect($createdShop->exists())->toBeTrue();
    expect($createdShop->name)->toBe($shopName);
    expect($createdShop->xpath_price)->toBe($xPath);
});

