<?php

namespace App\Common;

use App\Repository\UserRepository;

class NameGenerator
{
    private const NAMES = [
        ['Flexing', 'Redpilled', 'Yapper'],
        ['Skibidi', 'Mewing', 'Rizzler'],
        ['Glowing', 'Mogging', 'Furry'],
        ['Ohio', 'Rizzing', 'Chad'],
        ['Cute', 'Salty', 'Rock'],
        ['Squishy', 'Breedable', 'Femboy'],
        ['Tweaking', 'Shook', 'Pookie'],

        ['Woke', 'Red', 'Panda'],
        ['Silly', 'Orange', 'Cat'],
        ['Based', 'Yellow', 'Goat'],
        ['Balling', 'Green', 'Bat'],
        ['Sussy', 'Cyan', 'Dog'],
        ['Vibing', 'Blue', 'Bull'],
        ['Zesty', 'Violet', 'Bird'],
        ['Goated', 'Purple', 'Owl'],

        ['Chilling', 'Cold', 'Apple'],
        ['Sweating', 'Hot', 'Pear'],
        ['Glazed', 'Warm', 'Grape'],
        ['Drippy', 'Chill', 'Cherry'],
        ['Basic', 'Lukewarm', 'Banana'],

        ['Bussing', 'Round', 'Tomato'],
        ['Cooked', 'Square', 'Cookie'],
        ['Dabbing', 'Triangle', 'Pie'],
        ['Fire', 'Hexagonal', 'Cracker'],
        ['Lit', 'Flat', 'Bread'],
    ];

    public function __construct(
        private UserRepository $users,
    ) {}

    public function isUsernameOccupied(string $username): bool 
    {
        return $this->users->findByName($username) !== null;
    }

    public function getRandomUnoccupiedUsername(): string {
        $safety = 1000;
        do {
            $name = $this->generateName(random_bytes(4));

            if(!$this->isUsernameOccupied($name)) {
                break;
            }

            $name = null;

        } while($safety-- > 0);

        if(empty($name)) {
            throw new \Exception('Yet somehow, this happened!');
        }  

        return $name;
    }

    public function generateName(string $seed): string
    {
        $hash = (string) crc32($seed);


        $poolSize = count(self::NAMES);

        $part1 = ((int) substr($hash, 0, 2)) % $poolSize;
        $part2 = ((int) substr($hash, 2, 2)) % $poolSize;
        $part3 = ((int) substr($hash, 4, 2)) % $poolSize;

        return join('', [
            self::NAMES[$part1][0],
            self::NAMES[$part2][1],
            self::NAMES[$part3][2],
        ]);

    }
}
